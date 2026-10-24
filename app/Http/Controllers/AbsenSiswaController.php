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
 * Alur "satu pintu": absen HANYA bisa dimulai dari kartu jadwal hari ini di dashboard.
 * Tanggal tidak pernah diterima dari browser; selalu tanggal hari ini (WIB).
 */
class AbsenSiswaController extends Controller
{
    // =====================================================================
    //  FORM ABSEN (dibuka dari kartu jadwal di dashboard)
    // =====================================================================
    public function form(Jadwal $jadwal)
    {
        $now   = Absen::sekarang();
        $kartu = Absen::kartuUntuk(Auth::user(), $jadwal->id, $now);

        if ($tolak = $this->tolak($kartu)) {
            return $tolak;
        }

        $siswas    = Absen::siswaKelas($kartu->kelas);
        $pertemuan = Absen::pertemuanBerikutnya($kartu->kelas, $kartu->mapel, $now->toDateString());

        return view('guru.dashboard_absen_siswa', compact('jadwal', 'kartu', 'siswas', 'pertemuan', 'now'));
    }

    // =====================================================================
    //  SIMPAN ABSEN -> kembali ke dashboard + toast
    // =====================================================================
    public function simpan(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $request->validate([
            'presensi'   => 'required|array',
            'presensi.*' => 'in:MASUK,IZIN,SAKIT,ABSEN',
        ]);

        $user    = Auth::user();
        $now     = Absen::sekarang();
        $tanggal = $now->toDateString();          // dikunci: hari ini, tidak dari input
        $kartu   = Absen::kartuUntuk($user, $jadwal->id, $now);

        if ($tolak = $this->tolak($kartu)) {
            return $tolak;
        }

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

            return redirect()->route('guru.dashboard')
                ->with('error', "Kelas {$kartu->kelas} ({$kartu->mapel}) sudah diabsen hari ini.");
        }

        return redirect()->route('guru.dashboard')->with(
            'success',
            "Absen Kelas {$kartu->kelas} - {$kartu->mapel} (Pertemuan ke-{$pertemuan}) tersimpan: "
            . "Hadir {$hitung['MASUK']}, Izin {$hitung['IZIN']}, Sakit {$hitung['SAKIT']}, Alpa {$hitung['ABSEN']}."
        );
    }

    // =====================================================================
    //  REKAP ABSEN SISWA (matriks tanggal x siswa)
    // =====================================================================
    public function rekap(Request $request)
    {
        $user = Auth::user();
        $guru = Absen::guruDari($user);
        $opsi = $this->opsiKelasMapel($user, $guru);

        $kunci = (string) $request->query('kelas_mapel', '');
        $terpilih = $opsi->firstWhere('key', $kunci) ?? $opsi->first();

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
            'opsi'        => $opsi,
            'terpilih'    => $terpilih,
            'periodeList' => $periodeList,
            'periodeId'   => $periodeId,
            'dari'        => $dari->toDateString(),
            'sampai'      => $sampai->toDateString(),
            'guru'        => $guru,
            'kolom'       => collect(),
            'rekap'       => collect(),
            'liburRentang' => collect(),
        ];

        if (! $terpilih) {
            return view('guru.rekap_siswa', $data);
        }

        $kelas = $terpilih['kelas'];
        $mapel = $terpilih['mapel'];

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

        $liburRentang = HariLibur::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')->get()
            ->keyBy(fn ($l) => $l->tanggal->toDateString());

        $hariJadwal = Absen::whereKelas(Jadwal::query(), 'kelas', $kelas)
            ->whereRaw('lower(mapel) = ?', [mb_strtolower($mapel)])
            ->pluck('hari')
            ->map(fn ($h) => mb_strtolower((string) $h))
            ->unique()->all();

        // ---- kolom tanggal ----
        $kolom = [];
        $nilai = [];

        foreach ($rows as $r) {
            $tgl = substr((string) $r->tanggal, 0, 10);

            if (! isset($kolom[$tgl])) {
                $kolom[$tgl] = (object) [
                    'tanggal'    => $tgl,
                    'carbon'     => Carbon::parse($tgl, Absen::TZ),
                    'libur'      => false,
                    'keterangan' => null,
                    'guru'       => $r->nama_guru ?: '-',
                    'pengganti'  => (bool) $r->is_pengganti,
                ];
            }

            $nilai[$r->siswa_id][$tgl] = Absen::STATUS[$r->status] ?? '?';
        }

        // Tanggal merah ikut tampil bila ada data hari itu, atau kelas ini memang punya jadwal di hari tsb.
        foreach ($liburRentang as $tgl => $libur) {
            $carbon = Carbon::parse($tgl, Absen::TZ);

            if (isset($kolom[$tgl]) || in_array(mb_strtolower(Absen::namaHari($carbon)), $hariJadwal, true)) {
                $kolom[$tgl] = (object) [
                    'tanggal'    => $tgl,
                    'carbon'     => $carbon,
                    'libur'      => true,
                    'keterangan' => $libur->keterangan,
                    'guru'       => null,
                    'pengganti'  => false,
                ];
            }
        }

        ksort($kolom);

        // ---- total & persentase (hari libur tidak dihitung) ----
        $rekap = $siswas->map(function ($s) use ($kolom, $nilai) {
            $total = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
            $sel   = [];

            foreach ($kolom as $tgl => $k) {
                if ($k->libur) {
                    $sel[$tgl] = 'LIBUR';
                    continue;
                }

                $huruf = $nilai[$s->id][$tgl] ?? null;
                $sel[$tgl] = $huruf;

                if ($huruf && isset($total[$huruf])) {
                    $total[$huruf]++;
                }
            }

            $efektif = array_sum($total);

            return (object) [
                'id'      => $s->id,
                'nama'    => $s->nama,
                'sel'     => $sel,
                'total'   => $total,
                'persen'  => $efektif > 0 ? round($total['H'] / $efektif * 100, 1) : null,
            ];
        });

        return view('guru.rekap_siswa', array_merge($data, [
            'kolom'        => collect($kolom),
            'rekap'        => $rekap,
            'liburRentang' => $liburRentang,
        ]));
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
