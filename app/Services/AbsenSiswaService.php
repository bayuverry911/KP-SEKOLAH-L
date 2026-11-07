<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\JadwalPengganti;
use App\Models\Kelas;
use App\Models\PeriodePembelajaran;
use App\Models\TahunPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Logika bersama untuk absensi siswa berbasis jadwal:
 * dipakai oleh dashboard guru, halaman absen, dan rekap.
 */
class AbsenSiswaService
{
    public const TZ = 'Asia/Jakarta';

    public const HARI = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
    ];

    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /** Nilai di database => huruf di rekap */
    public const STATUS = ['MASUK' => 'H', 'IZIN' => 'I', 'SAKIT' => 'S', 'ABSEN' => 'A'];

    public static function sekarang(): Carbon
    {
        return Carbon::now(self::TZ);
    }

    public static function namaHari(Carbon $tanggal): string
    {
        return self::HARI[$tanggal->dayOfWeekIso];
    }

    /** "Selasa, 20 Oktober 2026" (tidak bergantung pada pengaturan locale aplikasi) */
    public static function tanggalPanjang(Carbon $tanggal): string
    {
        return self::namaHari($tanggal) . ', ' . $tanggal->day . ' '
            . self::BULAN[$tanggal->month] . ' ' . $tanggal->year;
    }

    /** "Kelas 7 A" / "7a" / "7A" => "7a" (untuk pencocokan nama kelas) */
    public static function normalKelas(?string $kelas): string
    {
        return preg_replace('/kelas|\s/', '', mb_strtolower((string) $kelas));
    }

    /** Filter kolom kelas (PostgreSQL) yang tidak peka spasi / kata "Kelas" / huruf besar-kecil. */
    public static function whereKelas($query, string $kolom, ?string $kelas)
    {
        return $query->whereRaw(
            "regexp_replace(lower({$kolom}), 'kelas|\\s', '', 'g') = ?",
            [self::normalKelas($kelas)]
        );
    }

    public static function guruDari(User $user): ?Guru
    {
        $guru = Guru::where('user_id', $user->id)->first();

        if (! $guru && trim((string) $user->name) !== '') {
            $guru = Guru::where('nama', 'ILIKE', '%' . trim($user->name) . '%')->first();
        }

        return $guru;
    }

    public static function hariLibur(Carbon $tanggal): ?HariLibur
    {
        return HariLibur::whereDate('tanggal', $tanggal->toDateString())->first();
    }

    public static function sudahDiabsen(string $kelas, string $mapel, string $tanggal): bool
    {
        $q = DB::table('absensi_siswas')
            ->where('tanggal', $tanggal)
            ->whereRaw('lower(mapel) = ?', [mb_strtolower($mapel)]);

        return self::whereKelas($q, 'kelas', $kelas)->exists();
    }

    /** Pertemuan ke-berapa untuk kelas + mapel ini (berdasarkan jumlah hari yang sudah diabsen). */
    public static function pertemuanBerikutnya(string $kelas, string $mapel, string $tanggal): int
    {
        $q = DB::table('absensi_siswas')
            ->where('tanggal', '<', $tanggal)
            ->whereRaw('lower(mapel) = ?', [mb_strtolower($mapel)]);

        return self::whereKelas($q, 'kelas', $kelas)->distinct()->count('tanggal') + 1;
    }

    /** Tahun Pelajaran yang mencakup tanggal tsb; bila tidak ada, yang aktif. */
    public static function tahunPada(?Carbon $tanggal = null): ?TahunPelajaran
    {
        $tanggal ??= self::sekarang();

        return TahunPelajaran::whereDate('tanggal_mulai', '<=', $tanggal->toDateString())
            ->whereDate('tanggal_selesai', '>=', $tanggal->toDateString())
            ->orderByDesc('aktif')->first()
            ?? TahunPelajaran::aktif();
    }

    /**
     * Siswa pada sebuah kelas di tahun pelajaran yang mencakup $acuan (default: hari ini).
     *
     * Penempatan resmi dibaca dari tabel riwayat `kelas_siswas` (hasil Kenaikan Kelas).
     * Siswa yang belum punya catatan pada tahun tsb memakai kelas di profilnya (siswas.kelas),
     * yang TIDAK pernah ditimpa. Siswa berstatus lulus tidak ikut.
     */
    public static function siswaKelas(string $kelas, ?Carbon $acuan = null): Collection
    {
        return self::siswaKelasTahun($kelas, self::tahunPada($acuan));
    }

    /** Siswa pada sebuah kelas di Tahun Pelajaran tertentu (null = hanya profil siswa). */
    public static function siswaKelasTahun(string $kelas, ?TahunPelajaran $tahun): Collection
    {
        $q = DB::table('siswas')
            ->leftJoin('kelas_siswas as ks', function ($j) use ($tahun) {
                $j->on('ks.siswa_id', '=', 'siswas.id');

                if ($tahun) {
                    $j->where('ks.tahun_pelajaran_id', '=', $tahun->id);
                } else {
                    $j->whereRaw('1 = 0');
                }
            })
            ->leftJoin('kelas as kl', 'kl.id', '=', 'ks.kelas_id')
            ->whereRaw("coalesce(ks.status, 'aktif') = 'aktif'")
            ->whereRaw(
                "regexp_replace(lower(coalesce(kl.nama_kelas, siswas.kelas)), 'kelas|\\s', '', 'g') = ?",
                [self::normalKelas($kelas)]
            )
            ->orderBy('siswas.nama');

        return $q->get([
            'siswas.id', 'siswas.nama', 'siswas.nisn',
            DB::raw('coalesce(kl.nama_kelas, siswas.kelas) as kelas'),
        ]);
    }

    /**
     * Daftar jadwal HARI INI untuk seorang guru: jadwal miliknya
     * + jadwal guru lain yang menugaskannya sebagai pengganti hari ini.
     *
     * Status kartu: mulai | belum | sudah | libur | digantikan
     */
    public static function jadwalHariIni(User $user, ?Carbon $now = null): Collection
    {
        $now ??= self::sekarang();
        $guru = self::guruDari($user);

        if (! $guru) {
            return collect();
        }

        $tanggal = $now->toDateString();
        $hari    = self::namaHari($now);
        $libur   = self::hariLibur($now);

        $penggantiHariIni = JadwalPengganti::where('tanggal', $tanggal)->get()->keyBy('jadwal_id');

        $jadwalSendiri = Jadwal::whereRaw('lower(hari) = ?', [mb_strtolower($hari)])
            ->where(function ($q) use ($guru) {
                $q->where('guru_id', (string) $guru->id)
                  ->orWhere(function ($q2) use ($guru) {
                      $q2->whereNull('guru_id')->where('nama_guru', 'ILIKE', $guru->nama);
                  });
            })
            ->get();

        $idPengganti = $penggantiHariIni
            ->where('guru_id', (string) $guru->id)
            ->pluck('jadwal_id')
            ->all();

        $jadwalPengganti = $idPengganti
            ? Jadwal::whereIn('id', $idPengganti)->get()
            : collect();

        $hasil = collect();

        foreach ($jadwalSendiri as $j) {
            $hasil->push(self::buatKartu(
                $j, $now, $libur, false, null, optional($penggantiHariIni->get($j->id))->nama_guru
            ));
        }

        foreach ($jadwalPengganti as $j) {
            if ($hasil->contains(fn ($k) => $k->id === $j->id)) {
                continue;
            }
            $hasil->push(self::buatKartu($j, $now, $libur, true, $j->nama_guru, null));
        }

        return $hasil->sortBy('jam_masuk')->values();
    }

    /** Cari kartu jadwal hari ini milik user untuk jadwal tertentu (null = tidak berhak). */
    public static function kartuUntuk(User $user, int $jadwalId, ?Carbon $now = null): ?object
    {
        return self::jadwalHariIni($user, $now)->firstWhere('id', $jadwalId);
    }

    /**
     * ABSENSI UTAMA (jam ke-1): satu kali per kelas per hari, dilakukan guru yang mengajar
     * di jam pertama kelas tersebut hari ini (atau guru penggantinya).
     * Terpisah dari absensi mapel (yang dipakai untuk % kehadiran).
     *
     * Status kartu: mulai | belum | sudah (terkunci) | libur
     */
    public static function utamaHariIni(User $user, ?Carbon $now = null): Collection
    {
        $now ??= self::sekarang();
        $guru = self::guruDari($user);

        if (! $guru) {
            return collect();
        }

        $tanggal = $now->toDateString();
        $hari    = mb_strtolower(self::namaHari($now));
        $libur   = self::hariLibur($now);

        // Kelas yang diajar guru ini hari ini (jadwal sendiri yang tidak digantikan + jadwal pengganti)
        $daftarKelas = self::jadwalHariIni($user, $now)
            ->reject(fn ($k) => $k->diganti_oleh)
            ->pluck('kelas')
            ->unique(fn ($k) => self::normalKelas($k));

        $penggantiHariIni = JadwalPengganti::where('tanggal', $tanggal)->get()->keyBy('jadwal_id');
        $toleransi        = (int) config('sekolah.absen_siswa_toleransi_menit', 0);
        $hasil            = collect();

        foreach ($daftarKelas as $kelas) {
            // Jam ke-1 = jadwal paling awal untuk kelas ini hari ini (semua guru)
            $j1 = self::whereKelas(Jadwal::query(), 'kelas', $kelas)
                ->whereRaw('lower(hari) = ?', [$hari])
                ->orderBy('jam_masuk')->orderBy('id')
                ->first();

            if (! $j1) {
                continue;
            }

            $p    = $penggantiHariIni->get($j1->id);
            $idPj = $p ? $p->guru_id : $j1->guru_id;

            $berhak = $idPj !== null
                ? (string) $guru->id === (string) $idPj
                : mb_strtolower((string) $j1->nama_guru) === mb_strtolower((string) $guru->nama);

            if (! $berhak) {
                continue;
            }

            $selesai = self::whereKelas(
                DB::table('absensi_utamas')->leftJoin('profiles', 'profiles.id', '=', 'absensi_utamas.user_id')
                    ->where('absensi_utamas.tanggal', $tanggal),
                'absensi_utamas.kelas',
                $j1->kelas
            )->first(['profiles.name as nama_guru']);

            $jamMasuk = substr((string) ($j1->jam_masuk ?: '00:00'), 0, 5);
            $mulai    = Carbon::parse("{$tanggal} {$jamMasuk}", self::TZ)->subMinutes($toleransi);

            $status = $libur ? 'libur'
                : ($selesai ? 'sudah'
                : ($now->lt($mulai) ? 'belum' : 'mulai'));

            $hasil->push((object) [
                'jadwal_id'    => $j1->id,
                'kelas'        => $j1->kelas,
                'jam_masuk'    => $jamMasuk,
                'jumlah_siswa' => self::siswaKelas($j1->kelas)->count(),
                'status'       => $status,
                'selesai_oleh' => $selesai ? ($selesai->nama_guru ?: '-') : null,
                'is_pengganti' => $p !== null,
                'libur'        => $libur?->keterangan,
            ]);
        }

        return $hasil->sortBy('jam_masuk')->values();
    }

    public static function kartuUtamaUntuk(User $user, int $jadwalId, ?Carbon $now = null): ?object
    {
        return self::utamaHariIni($user, $now)->firstWhere('jadwal_id', $jadwalId);
    }

    /** Nama guru yang mengabsen kelas+mapel pada tanggal tsb (untuk kartu dashboard). */
    public static function namaPengabsen(string $kelas, string $mapel, string $tanggal): ?string
    {
        $q = DB::table('absensi_siswas')
            ->leftJoin('profiles', 'profiles.id', '=', 'absensi_siswas.user_id')
            ->where('absensi_siswas.tanggal', $tanggal)
            ->whereRaw('lower(absensi_siswas.mapel) = ?', [mb_strtolower($mapel)]);

        $r = self::whereKelas($q, 'absensi_siswas.kelas', $kelas)->first(['profiles.name as nama', 'absensi_siswas.is_pengganti']);

        if (! $r) {
            return null;
        }

        return ($r->nama ?: '-') . ($r->is_pengganti ? ' (Pengganti)' : '');
    }

    // =====================================================================
    //  REKAP ABSENSI UTAMA (sekolah, sekali per hari) - dipakai guru & admin
    // =====================================================================

    /** Seluruh kelas sekolah (Rekap Absensi Utama dapat dipantau semua guru & admin). */
    public static function semuaKelas(): Collection
    {
        return Kelas::orderBy('nama_kelas')->pluck('nama_kelas')
            ->sortBy(fn ($k) => $k, SORT_NATURAL | SORT_FLAG_CASE)->values();
    }

    /**
     * Status Absensi Utama sebuah kelas pada tanggal tertentu.
     * Dipakai di form absen mapel: guru mapel bisa membandingkan kehadiran sekolah (jam ke-1)
     * dengan kehadiran di jamnya, dan tahu siapa yang perlu diingatkan bila belum diisi.
     */
    public static function infoUtama(string $kelas, string $tanggal): array
    {
        $q = DB::table('absensi_utamas')
            ->leftJoin('profiles', 'profiles.id', '=', 'absensi_utamas.user_id')
            ->where('absensi_utamas.tanggal', $tanggal);

        $rows = self::whereKelas($q, 'absensi_utamas.kelas', $kelas)
            ->get(['absensi_utamas.siswa_id', 'absensi_utamas.status', 'profiles.name as nama']);

        $status = [];
        foreach ($rows as $r) {
            $status[$r->siswa_id] = self::STATUS[$r->status] ?? '?';
        }

        $penanggung = null;

        if ($rows->isEmpty()) {
            $tgl = Carbon::parse($tanggal, self::TZ);
            $j1  = self::whereKelas(Jadwal::query(), 'kelas', $kelas)
                ->whereRaw('lower(hari) = ?', [mb_strtolower(self::namaHari($tgl))])
                ->orderBy('jam_masuk')->orderBy('id')->first();

            if ($j1) {
                $p = JadwalPengganti::where('jadwal_id', $j1->id)->whereDate('tanggal', $tanggal)->first();
                $penanggung = $p ? $p->nama_guru : $j1->nama_guru;
            }
        }

        return [
            'ada'        => $rows->isNotEmpty(),
            'status'     => $status,
            'oleh'       => $rows->first()->nama ?? null,
            'penanggung' => $penanggung,
        ];
    }

    /**
     * Ringkasan HARI INI untuk semua kelas: siapa guru jam ke-1 dan apakah Absensi Utama sudah diisi.
     * Status: sudah | belum (waktunya lewat) | menunggu (belum jam) | libur
     */
    public static function ringkasanUtamaHariIni(): Collection
    {
        $now     = self::sekarang();
        $tanggal = $now->toDateString();
        $hari    = mb_strtolower(self::namaHari($now));
        $libur   = self::hariLibur($now);

        $pengganti = JadwalPengganti::where('tanggal', $tanggal)->get()->keyBy('jadwal_id');

        $selesai = DB::table('absensi_utamas')
            ->leftJoin('profiles', 'profiles.id', '=', 'absensi_utamas.user_id')
            ->where('absensi_utamas.tanggal', $tanggal)
            ->get(['absensi_utamas.kelas', 'profiles.name as nama'])
            ->unique(fn ($r) => self::normalKelas($r->kelas))
            ->keyBy(fn ($r) => self::normalKelas($r->kelas));

        $hasil = collect();

        foreach (self::semuaKelas() as $kelas) {
            $j1 = self::whereKelas(Jadwal::query(), 'kelas', $kelas)
                ->whereRaw('lower(hari) = ?', [$hari])
                ->orderBy('jam_masuk')->orderBy('id')->first();

            if (! $j1) {
                continue; // tidak ada pelajaran hari ini
            }

            $p        = $pengganti->get($j1->id);
            $jamMasuk = substr((string) ($j1->jam_masuk ?: '00:00'), 0, 5);
            $sel      = $selesai->get(self::normalKelas($kelas));

            $status = $libur ? 'libur'
                : ($sel ? 'sudah'
                : ($now->lt(Carbon::parse("{$tanggal} {$jamMasuk}", self::TZ)) ? 'menunggu' : 'belum'));

            $hasil->push((object) [
                'kelas'        => $kelas,
                'jam_masuk'    => $jamMasuk,
                'guru'         => $p ? $p->nama_guru : $j1->nama_guru,
                'is_pengganti' => $p !== null,
                'status'       => $status,
                'oleh'         => $sel->nama ?? null,
            ]);
        }

        return $hasil->sortBy('jam_masuk')->values();
    }

    /** Daftar kelas yang diajar guru ini (jadwal sendiri + jadwal pengganti). */
    public static function kelasGuru(User $user): Collection
    {
        $guru = self::guruDari($user);

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

        return $jadwal->pluck('kelas')
            ->filter()
            ->unique(fn ($k) => self::normalKelas($k))
            ->sortBy(fn ($k) => $k, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Filter waktu rekap. Tiga mode terpisah:
     *  - bulan   : satu bulan penuh (input month) - default, bulan ini
     *  - tanggal : satu tanggal tunggal
     *  - rentang : periode pembelajaran / rentang tanggal manual
     */
    public static function rentang(Request $request): array
    {
        $periodeList = PeriodePembelajaran::orderBy('tanggal_mulai')->get();
        $periodeId   = $request->query('periode');
        $periode     = $periodeId ? $periodeList->firstWhere('id', (int) $periodeId) : null;
        $now         = self::sekarang();

        $parse = function ($nilai, string $format = 'Y-m-d') {
            if (! is_string($nilai) || $nilai === '') {
                return null;
            }
            try {
                $c = Carbon::createFromFormat($format, $nilai, self::TZ);

                return $c && $c->format($format) === $nilai ? $c->startOfDay() : null;
            } catch (\Throwable $e) {
                return null;
            }
        };

        $mode = $request->query('mode');

        if (! in_array($mode, ['bulan', 'tanggal', 'rentang'], true)) {
            $mode = ($request->filled('dari') && $request->filled('sampai')) || $request->filled('periode')
                ? 'rentang' : 'bulan';
        }

        $bulanAwal = $parse($request->query('bulan') ? $request->query('bulan') . '-01' : null);
        $bulan     = ($bulanAwal ?? $now->copy()->startOfMonth())->format('Y-m');
        $tanggal   = ($parse($request->query('tanggal')) ?? $now->copy()->startOfDay())->toDateString();

        if ($mode === 'bulan') {
            $dari   = Carbon::createFromFormat('Y-m-d', $bulan . '-01', self::TZ)->startOfDay();
            $sampai = $dari->copy()->endOfMonth()->startOfDay();
        } elseif ($mode === 'tanggal') {
            $dari   = Carbon::createFromFormat('Y-m-d', $tanggal, self::TZ)->startOfDay();
            $sampai = $dari->copy();
        } else {
            $dari   = $parse($request->query('dari'));
            $sampai = $parse($request->query('sampai'));

            if (! $dari || ! $sampai) {
                if ($periode) {
                    $dari   = $periode->tanggal_mulai->copy();
                    $sampai = $periode->tanggal_selesai->copy();
                } else {
                    $dari   = $now->copy()->startOfMonth();
                    $sampai = $now->copy()->endOfMonth()->startOfDay();
                }
            }

            if ($dari->gt($sampai)) {
                [$dari, $sampai] = [$sampai, $dari];
            }
            if ($dari->diffInDays($sampai) > 370) {
                $sampai = $dari->copy()->addDays(370);
            }
        }

        return compact('dari', 'sampai', 'periodeList', 'periodeId', 'mode', 'bulan', 'tanggal');
    }

    /** Data lengkap halaman Rekap Absensi Utama (view shared.rekap_utama). */
    public static function dataRekapUtama(Request $request, Collection $kelasList, string $aksi, $guru = null): array
    {
        $r = self::rentang($request);
        $dari = $r['dari'];
        $sampai = $r['sampai'];

        $mentah = (string) $request->query('kelas', '');
        $semua  = mb_strtoupper($mentah) === 'ALL' && $kelasList->isNotEmpty();
        $pilih  = self::normalKelas($mentah);
        $kelas  = $semua
            ? 'ALL'
            : ($kelasList->first(fn ($k) => self::normalKelas($k) === $pilih) ?? $kelasList->first());

        $hasil = null;

        if ($semua) {
            $perKelas = $kelasList->mapWithKeys(fn ($k) => [$k => self::rekapUtama($k, $dari, $sampai, null)]);
            $hasil    = self::gabungkanUtama($perKelas);
        } elseif ($kelas) {
            $hasil = self::rekapUtama($kelas, $dari, $sampai, $guru);
        }

        return [
            'aksi'             => $aksi,
            'kelasList'        => $kelasList,
            'kelas'            => $kelas,
            'semua'            => $semua,
            'bisaSusulan'      => $guru !== null,
            'periodeList'      => $r['periodeList'],
            'periodeId'        => $r['periodeId'],
            'mode'             => $r['mode'],
            'bulan'            => $r['bulan'],
            'tanggalPilih'     => $r['tanggal'],
            'dari'             => $dari->toDateString(),
            'sampai'           => $sampai->toDateString(),
            'hasil'            => $hasil,
            'ringkasanHariIni' => self::ringkasanUtamaHariIni(),
        ];
    }

    /** Siapa yang berhak mengisi Absensi Utama kelas ini pada tanggal tsb (guru jam ke-1 / penggantinya). */
    public static function infoSusulanUtama(string $kelas, Carbon $tgl, $guru): ?array
    {
        $j1 = self::whereKelas(Jadwal::query(), 'kelas', $kelas)
            ->whereRaw('lower(hari) = ?', [mb_strtolower(self::namaHari($tgl))])
            ->orderBy('jam_masuk')->orderBy('id')->first();

        if (! $j1) {
            return null;
        }

        $p     = JadwalPengganti::where('jadwal_id', $j1->id)->whereDate('tanggal', $tgl->toDateString())->first();
        $idPj  = $p ? $p->guru_id : $j1->guru_id;
        $nama  = $p ? $p->nama_guru : $j1->nama_guru;

        $boleh = $guru && (
            $idPj !== null
                ? (string) $guru->id === (string) $idPj
                : mb_strtolower((string) $j1->nama_guru) === mb_strtolower((string) $guru->nama)
        );

        return ['jadwal_id' => $j1->id, 'boleh' => (bool) $boleh, 'nama' => $nama, 'pengganti' => $p !== null];
    }

    /**
     * Matriks Absensi Utama satu kelas untuk SEMUA hari sekolah dalam rentang
     * (termasuk yang belum terjadi). Hari efektif = tanggal yang sudah diabsen utama.
     * state kolom: sudah | belum (terlewat) | libur | hariini | depan
     */
    public static function rekapUtama(string $kelas, Carbon $dari, Carbon $sampai, $guru = null): array
    {
        $siswas = self::siswaKelas($kelas, $dari);

        $q = DB::table('absensi_utamas')
            ->leftJoin('profiles', 'profiles.id', '=', 'absensi_utamas.user_id')
            ->whereBetween('absensi_utamas.tanggal', [$dari->toDateString(), $sampai->toDateString()]);
        self::whereKelas($q, 'absensi_utamas.kelas', $kelas);

        $rows = $q->get(['absensi_utamas.siswa_id', 'absensi_utamas.tanggal', 'absensi_utamas.status', 'profiles.name as nama_guru']);

        $hariJadwal = self::whereKelas(Jadwal::query(), 'kelas', $kelas)
            ->pluck('hari')->map(fn ($h) => mb_strtolower((string) $h))->unique()->all();

        $liburRentang = HariLibur::whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->orderBy('tanggal')->get()
            ->keyBy(fn ($l) => $l->tanggal->toDateString());

        $hariIni = self::sekarang()->startOfDay();
        $kolom   = [];
        $nilai   = [];

        foreach ($rows as $r) {
            $tgl = substr((string) $r->tanggal, 0, 10);

            $kolom[$tgl] ??= (object) [
                'tanggal' => $tgl, 'carbon' => Carbon::parse($tgl, self::TZ), 'state' => 'sudah',
                'keterangan' => null, 'oleh' => $r->nama_guru ?: '-', 'susulan' => null,
            ];

            $nilai[$r->siswa_id][$tgl] = self::STATUS[$r->status] ?? '?';
        }

        for ($d = $dari->copy()->startOfDay(); $d->lte($sampai); $d->addDay()) {
            $tgl       = $d->toDateString();
            $terjadwal = in_array(mb_strtolower(self::namaHari($d)), $hariJadwal, true);
            $libur     = $liburRentang->get($tgl);
            $adaData   = isset($kolom[$tgl]);

            if ($libur && ($adaData || $terjadwal)) {
                $kolom[$tgl] = (object) [
                    'tanggal' => $tgl, 'carbon' => $d->copy(), 'state' => 'libur',
                    'keterangan' => $libur->keterangan, 'oleh' => null, 'susulan' => null,
                ];
            } elseif (! $adaData && $terjadwal) {
                $state = $d->lt($hariIni) ? 'belum' : ($d->eq($hariIni) ? 'hariini' : 'depan');

                $kolom[$tgl] = (object) [
                    'tanggal' => $tgl, 'carbon' => $d->copy(), 'state' => $state,
                    'keterangan' => null, 'oleh' => null,
                    'susulan' => ($state === 'belum' && $guru) ? self::infoSusulanUtama($kelas, $d, $guru) : null,
                ];
            }
        }

        ksort($kolom);
        $kolom = collect($kolom);

        $efektif    = $kolom->where('state', 'sudah')->count();
        $belumCount = $kolom->where('state', 'belum')->count();

        $rekap = $siswas->map(function ($s) use ($kolom, $nilai, $efektif, $kelas) {
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
                'nama' => $s->nama, 'nisn' => $s->nisn ?? null, 'kelas' => $kelas,
                'sel' => $sel, 'st' => $st, 'total' => $total,
                'persen' => $efektif > 0 ? round($total['H'] / $efektif * 100, 1) : null,
            ];
        });

        return compact('kolom', 'rekap', 'efektif', 'belumCount', 'liburRentang');
    }

    /** Gabungkan rekap Absensi Utama banyak kelas menjadi satu tabel ("Semua Kelas"). */
    public static function gabungkanUtama(Collection $perKelas): array
    {
        $perTanggal = [];

        foreach ($perKelas as $hasil) {
            foreach ($hasil['kolom'] as $tgl => $k) {
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
                'tanggal' => $tgl, 'carbon' => $daftar[0]->carbon, 'state' => $agregat,
                'keterangan' => $libur->keterangan ?? null, 'oleh' => null, 'susulan' => null,
            ];
        });

        $baris = [];

        foreach ($perKelas as $hasil) {
            foreach ($hasil['rekap'] as $r) {
                $sel = [];
                $st  = [];

                foreach ($kolom as $tgl => $_) {
                    if (isset($hasil['kolom'][$tgl])) {
                        $sel[$tgl] = $r->sel[$tgl] ?? null;
                        $st[$tgl]  = $r->st[$tgl] ?? 'kosong';
                    } else {
                        $sel[$tgl] = null;
                        $st[$tgl]  = 'kosong';
                    }
                }

                $baris[] = (object) [
                    'nama' => $r->nama, 'nisn' => $r->nisn, 'kelas' => $r->kelas,
                    'sel' => $sel, 'st' => $st, 'total' => $r->total, 'persen' => $r->persen,
                ];
            }
        }

        $first = $perKelas->first();

        return [
            'kolom'        => $kolom,
            'rekap'        => collect($baris)->sortBy(fn ($r) => $r->kelas . '|' . mb_strtolower($r->nama), SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'efektif'      => 0,
            'belumCount'   => (int) $perKelas->sum('belumCount'),
            'liburRentang' => $first ? $first['liburRentang'] : collect(),
        ];
    }

    private static function buatKartu(
        Jadwal $j,
        Carbon $now,
        ?HariLibur $libur,
        bool $pengganti,
        ?string $guruAsli,
        ?string $digantiOleh
    ): object {
        $tanggal   = $now->toDateString();
        $jamMasuk  = substr((string) ($j->jam_masuk ?: '00:00'), 0, 5);
        $jamSelesai = substr((string) ($j->jam_selesai ?: '23:59'), 0, 5);
        $toleransi = (int) config('sekolah.absen_siswa_toleransi_menit', 0);
        $mulai     = Carbon::parse("{$tanggal} {$jamMasuk}", self::TZ)->subMinutes($toleransi);

        if ($libur) {
            $status = 'libur';
        } elseif (self::sudahDiabsen($j->kelas, $j->mapel, $tanggal)) {
            $status = 'sudah';
        } elseif ($digantiOleh) {
            $status = 'digantikan';
        } elseif ($now->lt($mulai)) {
            $status = 'belum';
        } else {
            $status = 'mulai';
        }

        return (object) [
            'id'            => $j->id,
            'kelas'         => $j->kelas,
            'mapel'         => $j->mapel,
            'jam_masuk'     => $jamMasuk,
            'jam_selesai'   => $jamSelesai,
            'jumlah_siswa'  => self::siswaKelas($j->kelas)->count(),
            'status'        => $status,
            'is_pengganti'  => $pengganti,
            'guru_asli'     => $guruAsli,
            'diganti_oleh'  => $digantiOleh,
            'libur'         => $libur?->keterangan,
            'diabsen_oleh'  => $status === 'sudah' ? self::namaPengabsen($j->kelas, $j->mapel, $tanggal) : null,
        ];
    }
}
