<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\JadwalPengganti;
use App\Models\User;
use Carbon\Carbon;
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

    public static function siswaKelas(string $kelas): Collection
    {
        return self::whereKelas(DB::table('siswas'), 'kelas', $kelas)
            ->orderBy('nama')
            ->get();
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
            'jumlah_siswa'  => self::whereKelas(DB::table('siswas'), 'kelas', $j->kelas)->count(),
            'status'        => $status,
            'is_pengganti'  => $pengganti,
            'guru_asli'     => $guruAsli,
            'diganti_oleh'  => $digantiOleh,
            'libur'         => $libur?->keterangan,
        ];
    }
}
