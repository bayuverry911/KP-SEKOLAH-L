<?php

namespace App\Http\Controllers;

use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\JadwalPengganti;
use App\Models\PeriodePembelajaran;
use App\Services\AbsenSiswaService as Absen;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Absensi siswa (sisi guru).
 *
 * Alur "satu pintu": absen dimulai dari kartu jadwal hari ini di dashboard,
 * atau dari tombol "Absen Susulan" di kolom kuning pada Rekap (tanggal terlewat).
 * Halaman absennya sama (bukan pop-up). Tanggal tidak pernah diterima dari form.
 */
class AbsenSiswaController extends Controller
{
    // =====================================================================
    //  ABSEN HARI INI (dari kartu di dashboard)
    // =====================================================================
    public function form(Jadwal $jadwal)
    {
        $now   = Absen::sekarang();
        $kartu = Absen::kartuUntuk(Auth::user(), $jadwal->id, $now);

        if ($tolak = $this->tolak($kartu)) {
            return $tolak;
        }

        return $this->tampilForm($jadwal, $kartu, $now, route('guru.absen-siswa.simpan', $jadwal->id), false);
    }

    public function simpan(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $now   = Absen::sekarang();
        $kartu = Absen::kartuUntuk(Auth::user(), $jadwal->id, $now);

        if ($tolak = $this->tolak($kartu)) {
            return $tolak;
        }

        return $this->simpanKeDb($request, $jadwal, $kartu, $now->toDateString(), false);
    }

    // =====================================================================
    //  ABSEN SUSULAN (tanggal terlewat yang belum diabsen)
    //  Hanya guru yang bertanggung jawab pada tanggal itu: guru pengganti
    //  bila ada yang di-plot admin, selain itu guru mapel pada jadwal.
    // =====================================================================
    public function formSusulan(Request $request, Jadwal $jadwal, string $tanggal)
    {
        [$kartu, $tgl, $error] = $this->siapkanSusulan($jadwal, $tanggal);
        $filter = $this->filterRekap($request, $jadwal, $tanggal);

        if ($error) {
            return redirect()->route('guru.rekap-siswa', $filter)->with('error', $error);
        }

        return $this->tampilForm(
            $jadwal, $kartu, $tgl,
            route('guru.absen-siswa.susulan.simpan', [$jadwal->id, $tanggal] + $filter),
            true,
            route('guru.rekap-siswa', $filter)
        );
    }

    public function simpanSusulan(Request $request, Jadwal $jadwal, string $tanggal): RedirectResponse
    {
        [$kartu, $tgl, $error] = $this->siapkanSusulan($jadwal, $tanggal);
        $filter = $this->filterRekap($request, $jadwal, $tanggal);

        if ($error) {
            return redirect()->route('guru.rekap-siswa', $filter)->with('error', $error);
        }

        // Selesai absen susulan -> langsung kembali ke Rekap (filter kelas & rentang yang sama)
        return $this->simpanKeDb($request, $jadwal, $kartu, $tgl->toDateString(), true, $filter);
    }

    private function tampilForm(Jadwal $jadwal, object $kartu, Carbon $tanggal, string $aksi, bool $susulan, ?string $kembali = null)
    {
        $siswas    = Absen::siswaKelas($kartu->kelas);
        $pertemuan = Absen::pertemuanBerikutnya($kartu->kelas, $kartu->mapel, $tanggal->toDateString());
        $now       = $tanggal; // tanggal absen (dikunci) yang ditampilkan di form

        return view('guru.dashboard_absen_siswa', compact('jadwal', 'kartu', 'siswas', 'pertemuan', 'now', 'aksi', 'susulan', 'kembali'));
    }

    /**
     * Validasi absen susulan. Mengembalikan [kartu, tanggal, pesanError].
     */
    private function siapkanSusulan(Jadwal $jadwal, string $tanggal): array
    {
        $gagal = fn (string $pesan) => [null, null, $pesan];

        try {
            $tgl = Carbon::createFromFormat('Y-m-d', $tanggal, Absen::TZ)->startOfDay();
        } catch (\Throwable $e) {
            return $gagal('Tanggal tidak valid.');
        }

        if ($tgl->toDateString() !== $tanggal) {
            return $gagal('Tanggal tidak valid.');
        }

        $tampil = $tgl->format('d/m/Y');

        if (! $tgl->lt(Absen::sekarang()->startOfDay())) {
            return $gagal('Absen susulan hanya untuk tanggal yang sudah lewat. Untuk hari ini gunakan kartu jadwal di dashboard.');
        }

        if (mb_strtolower((string) $jadwal->hari) !== mb_strtolower(Absen::namaHari($tgl))) {
            return $gagal("Tanggal {$tampil} bukan hari jadwal kelas ini ({$jadwal->hari}).");
        }

        if ($libur = Absen::hariLibur($tgl)) {
            return $gagal("Tanggal {$tampil} adalah hari libur ({$libur->keterangan}).");
        }

        if (Absen::sudahDiabsen($jadwal->kelas, $jadwal->mapel, $tanggal)) {
            return $gagal("Tanggal {$tampil} sudah diabsen.");
        }

        $guru      = Absen::guruDari(Auth::user());
        $pengganti = JadwalPengganti::where('jadwal_id', $jadwal->id)->whereDate('tanggal', $tanggal)->first();

        $idTanggungJawab   = $pengganti ? $pengganti->guru_id : $jadwal->guru_id;
        $namaTanggungJawab = $pengganti ? $pengganti->nama_guru : $jadwal->nama_guru;

        $berhak = $guru && (
            $idTanggungJawab !== null
                ? (string) $guru->id === (string) $idTanggungJawab
                : mb_strtolower((string) $jadwal->nama_guru) === mb_strtolower((string) $guru->nama)
        );

        if (! $berhak) {
            return $gagal(
                "Absen susulan tanggal {$tampil} hanya dapat diisi oleh "
                . ($pengganti ? 'guru pengganti ' : 'guru pengampu ') . $namaTanggungJawab . '.'
            );
        }

        $kartu = (object) [
            'id'           => $jadwal->id,
            'kelas'        => $jadwal->kelas,
            'mapel'        => $jadwal->mapel,
            'jam_masuk'    => substr((string) ($jadwal->jam_masuk ?: '00:00'), 0, 5),
            'jam_selesai'  => substr((string) ($jadwal->jam_selesai ?: '23:59'), 0, 5),
            'status'       => 'susulan',
            'is_pengganti' => $pengganti !== null,
            'guru_asli'    => $pengganti ? $jadwal->nama_guru : null,
        ];

        return [$kartu, $tgl, null];
    }

    /**
     * Simpan absen satu kelas untuk satu tanggal -> kembali ke dashboard + toast.
     * Data tercatat pada kelas + mapel (muncul di rekap guru mapel), user_id = guru yang mengabsen.
     */
    private function simpanKeDb(Request $request, Jadwal $jadwal, object $kartu, string $tanggal, bool $susulan, ?array $kembaliRekap = null): RedirectResponse
    {
        $request->validate([
            'presensi'   => 'required|array',
            'presensi.*' => 'in:MASUK,IZIN,SAKIT,ABSEN',
        ]);

        $user   = Auth::user();
        $siswas = Absen::siswaKelas($kartu->kelas);

        if ($siswas->isEmpty()) {
            return redirect()->route('guru.dashboard')
                ->with('error', "Kelas {$kartu->kelas} belum memiliki siswa terdaftar.");
        }

        $presensi = $request->input('presensi');

        foreach ($siswas as $s) {
            if (! isset($presensi[$s->id])) {
                return redirect()->back()
                    ->with('error', 'Data presensi tidak lengkap (daftar siswa berubah). Muat ulang halaman lalu coba lagi.');
            }
        }

        $pertemuan = Absen::pertemuanBerikutnya($kartu->kelas, $kartu->mapel, $tanggal);
        $hitung    = ['MASUK' => 0, 'IZIN' => 0, 'SAKIT' => 0, 'ABSEN' => 0];

        try {
            DB::transaction(function () use ($siswas, $presensi, $kartu, $jadwal, $user, $tanggal, $pertemuan, &$hitung) {
                if (Absen::sudahDiabsen($kartu->kelas, $kartu->mapel, $tanggal)) {
                    throw new RuntimeException('sudah-diabsen');
                }

                $waktu = now();
                $rows  = [];

                foreach ($siswas as $s) {
                    $status = $presensi[$s->id];
                    $hitung[$status]++;

                    $rows[] = [
                        'user_id'      => $user->id,
                        'jadwal_id'    => $jadwal->id,
                        'is_pengganti' => $kartu->is_pengganti,
                        'siswa_id'     => $s->id,
                        'kelas'        => $kartu->kelas,
                        'mapel'        => $kartu->mapel,
                        'pertemuan_ke' => (string) $pertemuan,
                        'tanggal'      => $tanggal,
                        'status'       => $status,
                        'jam_masuk'    => $kartu->jam_masuk,
                        'jam_selesai'  => $kartu->jam_selesai,
                        'created_at'   => $waktu,
                        'updated_at'   => $waktu,
                    ];
                }

                DB::table('absensi_siswas')->insert($rows);
            });
        } catch (RuntimeException $e) {
            // QueryException juga turunan RuntimeException: hanya tangani bentrok absen ganda
            $duplikat = $e->getMessage() === 'sudah-diabsen'
                || ($e instanceof QueryException
                    && str_contains($e->getMessage(), 'absensi_siswas_jadwal_siswa_tanggal_unique'));

            if (! $duplikat) {
                throw $e;
            }

            return $this->kembali($kembaliRekap)
                ->with('error', "Kelas {$kartu->kelas} ({$kartu->mapel}) sudah diabsen pada tanggal tersebut.");
        }

        $tampil = Carbon::parse($tanggal)->format('d/m/Y');

        return $this->kembali($kembaliRekap)->with(
            'success',
            ($susulan ? "Absen susulan {$tampil} " : 'Absen ')
            . "Kelas {$kartu->kelas} - {$kartu->mapel} (Pertemuan ke-{$pertemuan}) tersimpan: "
            . "Hadir {$hitung['MASUK']}, Izin {$hitung['IZIN']}, Sakit {$hitung['SAKIT']}, Alpa {$hitung['ABSEN']}."
        );
    }

    /** Tujuan setelah simpan: Rekap (absen susulan) atau Dashboard (absen hari ini). */
    private function kembali(?array $kembaliRekap): RedirectResponse
    {
        return $kembaliRekap !== null
            ? redirect()->route('guru.rekap-siswa', $kembaliRekap)
            : redirect()->route('guru.dashboard');
    }

    /**
     * Filter Rekap yang dipertahankan saat absen susulan, supaya guru kembali ke
     * tampilan yang sama (kelas + rentang tanggal). Bila tidak ada, pakai kelas
     * jadwal ini dan bulan dari tanggal yang diabsen.
     */
    private function filterRekap(Request $request, Jadwal $jadwal, string $tanggal): array
    {
        $dari   = $this->parseTanggal($request->query('dari'));
        $sampai = $this->parseTanggal($request->query('sampai'));

        if (! $dari || ! $sampai) {
            $acuan  = $this->parseTanggal($tanggal) ?? Absen::sekarang();
            $dari   = $acuan->copy()->startOfMonth();
            $sampai = $acuan->copy()->endOfMonth();
        }

        $filter = [
            'kelas_mapel' => $jadwal->kelas . '|' . $jadwal->mapel,
            'dari'        => $dari->toDateString(),
            'sampai'      => $sampai->toDateString(),
        ];

        if ($request->filled('periode') && ctype_digit((string) $request->query('periode'))) {
            $filter['periode'] = (string) $request->query('periode');
        }

        return $filter;
    }

    // =====================================================================
    //  REKAP ABSEN SISWA
    //  - satu kelas: matriks lengkap + "Diabsen oleh" + Hapus Harian + Absen Susulan
    //  - ALL (semua kelas yang diajar): matriks ringkas, tanpa info pengabsen/hapus
    // =====================================================================
    public function rekap(Request $request)
    {
        $user = Auth::user();
        $guru = Absen::guruDari($user);
        $opsi = $this->opsiKelasMapel($user, $guru);

        $kunci = (string) $request->query('kelas_mapel', '');
        $semua = $kunci === 'ALL' && $opsi->isNotEmpty();

        $terpilih = $semua
            ? ['kelas' => 'Semua Kelas', 'mapel' => null, 'key' => 'ALL']
            : ($opsi->firstWhere('key', $kunci) ?? $opsi->first());

        // ---- rentang tanggal (periode pembelajaran atau manual) ----
        $periodeList = PeriodePembelajaran::orderBy('tanggal_mulai')->get();
        $periodeId   = $request->query('periode');
        $periode     = $periodeId ? $periodeList->firstWhere('id', (int) $periodeId) : null;

        $dari   = $this->parseTanggal($request->query('dari'));
        $sampai = $this->parseTanggal($request->query('sampai'));

        if (! $dari || ! $sampai) {
            if ($periode) {
                $dari   = $periode->tanggal_mulai->copy();
                $sampai = $periode->tanggal_selesai->copy();
            } else {
                $now    = Absen::sekarang();
                $dari   = $now->copy()->startOfMonth();
                $sampai = $now->copy()->endOfMonth();
            }
        }

        if ($dari->gt($sampai)) {
            [$dari, $sampai] = [$sampai, $dari];
        }
        if ($dari->diffInDays($sampai) > 370) {
            $sampai = $dari->copy()->addDays(370);
        }

        $data = [
            'opsi'         => $opsi,
            'terpilih'     => $terpilih,
            'semua'        => $semua,
            'periodeList'  => $periodeList,
            'periodeId'    => $periodeId,
            'dari'         => $dari->toDateString(),
            'sampai'       => $sampai->toDateString(),
            'guru'         => $guru,
            'kolom'        => collect(),
            'rekap'        => collect(),
            'liburRentang' => collect(),
            'efektif'      => 0,
            'belumCount'   => 0,
            'mapelBanyak'  => false,
        ];

        if (! $terpilih) {
            return view('guru.rekap_siswa', $data);
        }

        $liburRentang = HariLibur::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')->get()
            ->keyBy(fn ($l) => $l->tanggal->toDateString());

        if ($semua) {
            $hasil = $opsi->map(fn ($o) => ['o' => $o] + $this->hitungMatriks(
                $o['kelas'], $o['mapel'], $dari, $sampai, $guru, $liburRentang
            ));

            $gabungan = $this->gabungkan($hasil);

            return view('guru.rekap_siswa', array_merge($data, $gabungan, ['liburRentang' => $liburRentang]));
        }

        $m = $this->hitungMatriks($terpilih['kelas'], $terpilih['mapel'], $dari, $sampai, $guru, $liburRentang);

        return view('guru.rekap_siswa', array_merge($data, $m, ['liburRentang' => $liburRentang]));
    }

    /**
     * Matriks satu kelas + mapel: kolom tanggal (semua pertemuan terjadwal), baris siswa, total, %.
     *
     * state kolom: sudah | belum | libur | hariini | depan
     */
    private function hitungMatriks(string $kelas, string $mapel, Carbon $dari, Carbon $sampai, $guru, Collection $liburRentang): array
    {
        $siswas = Absen::siswaKelas($kelas);

        $q = DB::table('absensi_siswas')
            ->leftJoin('profiles', 'profiles.id', '=', 'absensi_siswas.user_id')
            ->whereBetween('absensi_siswas.tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->whereRaw('lower(absensi_siswas.mapel) = ?', [mb_strtolower($mapel)]);
        Absen::whereKelas($q, 'absensi_siswas.kelas', $kelas);

        $rows = $q->get([
            'absensi_siswas.siswa_id', 'absensi_siswas.tanggal', 'absensi_siswas.status',
            'absensi_siswas.is_pengganti', 'profiles.name as nama_guru',
        ]);

        $jadwals = Absen::whereKelas(Jadwal::query(), 'kelas', $kelas)
            ->whereRaw('lower(mapel) = ?', [mb_strtolower($mapel)])
            ->get();

        $hariJadwal = $jadwals->pluck('hari')->map(fn ($h) => mb_strtolower((string) $h))->unique()->all();

        $penggantiMap = $jadwals->isEmpty() ? collect() : JadwalPengganti::whereIn('jadwal_id', $jadwals->pluck('id'))
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->get()
            ->keyBy(fn ($p) => $p->jadwal_id . '|' . $p->tanggal->toDateString());

        $hariIni = Absen::sekarang()->startOfDay();
        $kolom   = [];
        $nilai   = [];

        foreach ($rows as $r) {
            $tgl = substr((string) $r->tanggal, 0, 10);

            if (! isset($kolom[$tgl])) {
                $kolom[$tgl] = (object) [
                    'tanggal'    => $tgl,
                    'carbon'     => Carbon::parse($tgl, Absen::TZ),
                    'state'      => 'sudah',
                    'keterangan' => null,
                    'guru'       => $r->nama_guru ?: '-',
                    'pengganti'  => (bool) $r->is_pengganti,
                    'susulan'    => null,
                ];
            }

            $nilai[$r->siswa_id][$tgl] = Absen::STATUS[$r->status] ?? '?';
        }

        $kursor = $dari->copy()->startOfDay();

        while ($kursor->lte($sampai)) {
            $tgl       = $kursor->toDateString();
            $namaHari  = mb_strtolower(Absen::namaHari($kursor));
            $terjadwal = in_array($namaHari, $hariJadwal, true);
            $libur     = $liburRentang->get($tgl);
            $adaData   = isset($kolom[$tgl]);

            if ($libur && ($adaData || $terjadwal)) {
                $kolom[$tgl] = (object) [
                    'tanggal' => $tgl, 'carbon' => $kursor->copy(), 'state' => 'libur',
                    'keterangan' => $libur->keterangan, 'guru' => null, 'pengganti' => false, 'susulan' => null,
                ];
            } elseif (! $adaData && $terjadwal) {
                $state = $kursor->lt($hariIni) ? 'belum' : ($kursor->eq($hariIni) ? 'hariini' : 'depan');

                $kolom[$tgl] = (object) [
                    'tanggal' => $tgl, 'carbon' => $kursor->copy(), 'state' => $state,
                    'keterangan' => null, 'guru' => null, 'pengganti' => false,
                    'susulan' => $state === 'belum'
                        ? $this->infoSusulan($jadwals, $penggantiMap, $namaHari, $tgl, $guru)
                        : null,
                ];
            }

            $kursor->addDay();
        }

        ksort($kolom);
        $kolom = collect($kolom);

        // Hari efektif = hanya tanggal yang SUDAH diabsen.
        // Libur, cuti bersama, belum diabsen, hari ini & masa depan tidak masuk pembagi.
        $efektif    = $kolom->where('state', 'sudah')->count();
        $belumCount = $kolom->where('state', 'belum')->count();

        $rekap = $siswas->map(function ($s) use ($kolom, $nilai, $efektif) {
            $total = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
            $sel   = [];
            $st    = [];

            foreach ($kolom as $tgl => $k) {
                $st[$tgl] = $k->state;

                if ($k->state !== 'sudah') {
                    $sel[$tgl] = null;
                    continue;
                }

                $huruf     = $nilai[$s->id][$tgl] ?? null;
                $sel[$tgl] = $huruf;

                if ($huruf && isset($total[$huruf])) {
                    $total[$huruf]++;
                }
            }

            return (object) [
                'id'     => $s->id,
                'nisn'   => $s->nisn ?? null,
                'nama'   => $s->nama,
                'sel'    => $sel,
                'st'     => $st,
                'total'  => $total,
                'persen' => $efektif > 0 ? round($total['H'] / $efektif * 100, 1) : null,
            ];
        });

        return compact('kolom', 'rekap', 'efektif', 'belumCount');
    }

    /** Siapa yang boleh mengisi absen susulan untuk tanggal terlewat ini. */
    private function infoSusulan(Collection $jadwals, Collection $penggantiMap, string $namaHari, string $tgl, $guru): ?array
    {
        $kandidat = $jadwals->filter(fn ($j) => mb_strtolower((string) $j->hari) === $namaHari);
        $pilihan  = null;

        foreach ($kandidat as $j) {
            $p      = $penggantiMap->get($j->id . '|' . $tgl);
            $idPj   = $p ? $p->guru_id : $j->guru_id;
            $namaPj = $p ? $p->nama_guru : $j->nama_guru;

            $boleh = $guru && (
                $idPj !== null
                    ? (string) $guru->id === (string) $idPj
                    : mb_strtolower((string) $j->nama_guru) === mb_strtolower((string) $guru->nama)
            );

            $info = ['jadwal_id' => $j->id, 'boleh' => (bool) $boleh, 'nama' => $namaPj, 'pengganti' => $p !== null];

            if ($boleh) {
                return $info;
            }

            $pilihan ??= $info;
        }

        return $pilihan;
    }

    /** Gabungkan matriks beberapa kelas menjadi satu tabel (mode "Semua Kelas"). */
    private function gabungkan(Collection $hasil): array
    {
        $perTanggal = [];

        foreach ($hasil as $h) {
            foreach ($h['kolom'] as $tgl => $k) {
                $perTanggal[$tgl][] = $k;
            }
        }

        ksort($perTanggal);

        $kolom = collect($perTanggal)->map(function ($daftar, $tgl) {
            $st = collect($daftar)->pluck('state');

            $agregat = $st->contains('belum') ? 'belum'
                : ($st->every(fn ($s) => $s === 'libur') ? 'libur'
                : ($st->contains('sudah') ? 'sudah'
                : ($st->contains('hariini') ? 'hariini' : 'depan')));

            $libur = collect($daftar)->firstWhere('state', 'libur');

            return (object) [
                'tanggal'    => $tgl,
                'carbon'     => $daftar[0]->carbon,
                'state'      => $agregat,
                'keterangan' => $libur->keterangan ?? null,
                'guru'       => null,
                'pengganti'  => false,
                'susulan'    => null,
            ];
        });

        $baris = [];

        foreach ($hasil as $h) {
            foreach ($h['rekap'] as $r) {
                $sel = [];
                $st  = [];

                foreach ($kolom as $tgl => $_) {
                    if (isset($h['kolom'][$tgl])) {
                        $sel[$tgl] = $r->sel[$tgl] ?? null;
                        $st[$tgl]  = $r->st[$tgl] ?? 'kosong';
                    } else {
                        $sel[$tgl] = null;
                        $st[$tgl]  = 'kosong';   // kelas ini tidak ada pertemuan di tanggal tsb
                    }
                }

                $baris[] = (object) [
                    'id'     => $r->id,
                    'nisn'   => $r->nisn,
                    'nama'   => $r->nama,
                    'kelas'  => $h['o']['kelas'],
                    'mapel'  => $h['o']['mapel'],
                    'sel'    => $sel,
                    'st'     => $st,
                    'total'  => $r->total,
                    'persen' => $r->persen,
                ];
            }
        }

        $rekap = collect($baris)
            ->sortBy(fn ($r) => $r->kelas . '|' . $r->mapel . '|' . mb_strtolower($r->nama), SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return [
            'kolom'       => $kolom,
            'rekap'       => $rekap,
            'efektif'     => 0,
            'belumCount'  => (int) $hasil->sum('belumCount'),
            'mapelBanyak' => $hasil->pluck('o.mapel')->map(fn ($m) => mb_strtolower((string) $m))->unique()->count() > 1,
        ];
    }

    // =====================================================================
    //  HAPUS ABSEN SATU HARI (seluruh siswa). Hapus per siswa tidak ada.
    // =====================================================================
    public function hapusHari(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kelas'   => 'required|string',
            'mapel'   => 'required|string',
            'tanggal' => 'required|date_format:Y-m-d',
        ]);

        $user = Auth::user();
        $opsi = $this->opsiKelasMapel($user, Absen::guruDari($user));

        $berhak = $opsi->contains(
            fn ($o) => Absen::normalKelas($o['kelas']) === Absen::normalKelas($data['kelas'])
                && mb_strtolower($o['mapel']) === mb_strtolower($data['mapel'])
        );

        $balik = $request->only(['kelas_mapel', 'periode', 'dari', 'sampai']);

        if (! $berhak) {
            return redirect()->route('guru.rekap-siswa', $balik)
                ->with('error', 'Anda tidak berhak menghapus absen kelas/mata pelajaran ini.');
        }

        $q = DB::table('absensi_siswas')
            ->where('tanggal', $data['tanggal'])
            ->whereRaw('lower(mapel) = ?', [mb_strtolower($data['mapel'])]);
        Absen::whereKelas($q, 'kelas', $data['kelas']);

        $jumlah = $q->delete();

        $tglTampil = Carbon::parse($data['tanggal'])->format('d/m/Y');

        return redirect()->route('guru.rekap-siswa', $balik)->with(
            $jumlah > 0 ? 'success' : 'error',
            $jumlah > 0
                ? "Absen tanggal {$tglTampil} (seluruh siswa Kelas {$data['kelas']}) berhasil dihapus. Silakan absen ulang dari dashboard bila perlu."
                : "Tidak ada data absen pada tanggal {$tglTampil}."
        );
    }

    // =====================================================================
    //  Helper
    // =====================================================================

    /** Kembalikan redirect bila kartu tidak boleh dipakai absen; null bila boleh. */
    private function tolak(?object $kartu): ?RedirectResponse
    {
        $pesan = match (true) {
            $kartu === null                    => 'Jadwal ini tidak tersedia untuk Anda pada hari ini.',
            $kartu->status === 'libur'         => "Hari ini libur ({$kartu->libur}). Absensi tidak dapat dilakukan.",
            $kartu->status === 'sudah'         => "Kelas {$kartu->kelas} ({$kartu->mapel}) sudah diabsen hari ini. Untuk mengoreksi, hapus absen hari ini lewat Rekap Absen Siswa, lalu absen ulang.",
            $kartu->status === 'digantikan'    => "Jadwal ini hari ini digantikan oleh {$kartu->diganti_oleh}.",
            $kartu->status === 'belum'         => "Belum waktunya. Absen kelas ini dibuka mulai pukul {$kartu->jam_masuk} WIB.",
            default                            => null,
        };

        return $pesan ? redirect()->route('guru.dashboard')->with('error', $pesan) : null;
    }

    /**
     * Pasangan kelas + mapel yang boleh dilihat/diatur guru ini
     * (dari jadwalnya sendiri dan jadwal yang pernah/akan ia gantikan).
     */
    private function opsiKelasMapel($user, $guru): Collection
    {
        if (! $guru) {
            return collect();
        }

        $jadwal = Jadwal::where(function ($q) use ($guru) {
            $q->where('guru_id', (string) $guru->id)
              ->orWhere(function ($q2) use ($guru) {
                  $q2->whereNull('guru_id')->where('nama_guru', 'ILIKE', $guru->nama);
              });
        })->get();

        $idGanti = JadwalPengganti::where('guru_id', (string) $guru->id)->pluck('jadwal_id');
        if ($idGanti->isNotEmpty()) {
            $jadwal = $jadwal->merge(Jadwal::whereIn('id', $idGanti)->get());
        }

        $opsi = $jadwal
            ->filter(fn ($j) => filled($j->kelas) && filled($j->mapel))
            ->map(fn ($j) => ['kelas' => $j->kelas, 'mapel' => $j->mapel])
            ->unique(fn ($o) => Absen::normalKelas($o['kelas']) . '|' . mb_strtolower($o['mapel']));

        // Cadangan: guru belum punya jadwal -> pakai data kelas & mapel di profil guru
        if ($opsi->isEmpty() && filled($guru->mata_pelajaran)) {
            $mentah = (string) $guru->getRawOriginal('kelas');
            $daftar = json_decode($mentah, true);
            $daftar = is_array($daftar) ? $daftar : array_filter(array_map('trim', explode(',', $mentah)));

            $opsi = collect($daftar)->map(fn ($k) => ['kelas' => $k, 'mapel' => $guru->mata_pelajaran]);
        }

        return $opsi
            ->sortBy(fn ($o) => $o['kelas'] . '|' . $o['mapel'], SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn ($o) => $o + ['key' => $o['kelas'] . '|' . $o['mapel']])
            ->values();
    }

    private function parseTanggal($nilai): ?Carbon
    {
        if (! is_string($nilai) || $nilai === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $nilai, Absen::TZ)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
