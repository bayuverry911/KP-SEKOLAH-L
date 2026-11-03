<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Services\AbsenSiswaService as Absen;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Absensi Utama (jam ke-1): sekali per kelas per hari oleh guru jam pertama.
 * Setelah disubmit, form terkunci dan menampilkan siapa yang menyelesaikan.
 * Terpisah dari absensi mapel (yang dipakai untuk persentase kehadiran).
 * Tanggal yang terlewat dapat diisi lewat Absen Susulan (hanya guru jam ke-1 / penggantinya).
 */
class AbsenUtamaController extends Controller
{
    // ------------------------------------------------------------------ hari ini
    public function form(Jadwal $jadwal)
    {
        $now   = Absen::sekarang();
        $kartu = Absen::kartuUtamaUntuk(Auth::user(), $jadwal->id, $now);

        if ($pesan = $this->pesanTolak($kartu)) {
            return redirect()->route('guru.dashboard')->with('error', $pesan);
        }

        $siswas    = Absen::siswaKelas($kartu->kelas, $now);
        $terkunci  = $kartu->status === 'sudah';
        $tersimpan = [];

        if ($terkunci) {
            $q = DB::table('absensi_utamas')->where('tanggal', $now->toDateString());
            $tersimpan = Absen::whereKelas($q, 'kelas', $kartu->kelas)->pluck('status', 'siswa_id')->all();
        }

        return $this->tampil($jadwal, $kartu, $siswas, $now, route('guru.absen-utama.simpan', $jadwal->id), false, null, $terkunci, $tersimpan);
    }

    public function simpan(Request $request, Jadwal $jadwal): RedirectResponse
    {
        $now   = Absen::sekarang();
        $kartu = Absen::kartuUtamaUntuk(Auth::user(), $jadwal->id, $now);

        if ($pesan = $this->pesanTolak($kartu)) {
            return redirect()->route('guru.dashboard')->with('error', $pesan);
        }

        if ($kartu->status !== 'mulai') {
            return redirect()->route('guru.dashboard')->with('error', 'Absensi Utama kelas ini belum dapat diisi.');
        }

        return $this->simpanKeDb($request, $jadwal, $kartu, $now->toDateString(), null);
    }

    // ------------------------------------------------------------------ susulan
    public function formSusulan(Request $request, Jadwal $jadwal, string $tanggal)
    {
        [$kartu, $tgl, $error] = $this->siapkanSusulan($jadwal, $tanggal);
        $filter = $this->filterRekap($request, $jadwal);

        if ($error) {
            return redirect()->route('guru.rekap-utama', $filter)->with('error', $error);
        }

        return $this->tampil(
            $jadwal, $kartu, Absen::siswaKelas($kartu->kelas, $tgl), $tgl,
            route('guru.absen-utama.susulan.simpan', [$jadwal->id, $tanggal] + $filter),
            true, route('guru.rekap-utama', $filter), false, []
        );
    }

    public function simpanSusulan(Request $request, Jadwal $jadwal, string $tanggal): RedirectResponse
    {
        [$kartu, $tgl, $error] = $this->siapkanSusulan($jadwal, $tanggal);
        $filter = $this->filterRekap($request, $jadwal);

        if ($error) {
            return redirect()->route('guru.rekap-utama', $filter)->with('error', $error);
        }

        return $this->simpanKeDb($request, $jadwal, $kartu, $tgl->toDateString(), $filter);
    }

    // ------------------------------------------------------------------ rekap
    /** Rekap Absensi Utama untuk guru: seluruh kelas (semua guru dapat memantau). */
    public function rekap(Request $request)
    {
        $guru = Absen::guruDari(Auth::user());

        return view('shared.rekap_utama', Absen::dataRekapUtama(
            $request, Absen::semuaKelas(), route('guru.rekap-utama'), $guru
        ));
    }

    // ------------------------------------------------------------------ helper
    private function tampil(Jadwal $jadwal, object $kartu, $siswas, Carbon $tanggal, string $aksi, bool $susulan, ?string $kembali, bool $terkunci, array $tersimpan)
    {
        // Memakai tampilan yang sama dengan absen siswa (dashboard_absen_siswa)
        return view('guru.dashboard_absen_siswa', [
            'jadwal'      => $jadwal,
            'kartu'       => $kartu,
            'siswas'      => $siswas,
            'pertemuan'   => null,
            'now'         => $tanggal,
            'aksi'        => $aksi,
            'susulan'     => $susulan,
            'kembali'     => $kembali,
            'utama'       => true,
            'terkunci'    => $terkunci,
            'tersimpan'   => $tersimpan,
            'selesaiOleh' => $kartu->selesai_oleh ?? null,
        ]);
    }

    private function simpanKeDb(Request $request, Jadwal $jadwal, object $kartu, string $tanggal, ?array $kembaliRekap): RedirectResponse
    {
        $request->validate([
            'presensi'   => 'required|array',
            'presensi.*' => 'in:MASUK,IZIN,SAKIT,ABSEN',
        ]);

        $user   = Auth::user();
        $siswas = Absen::siswaKelas($kartu->kelas, Carbon::parse($tanggal, Absen::TZ));
        $balik  = fn () => $kembaliRekap !== null
            ? redirect()->route('guru.rekap-utama', $kembaliRekap)
            : redirect()->route('guru.dashboard');

        if ($siswas->isEmpty()) {
            return $balik()->with('error', "Kelas {$kartu->kelas} belum memiliki siswa terdaftar.");
        }

        $presensi = $request->input('presensi');

        foreach ($siswas as $s) {
            if (! isset($presensi[$s->id])) {
                return redirect()->back()->with('error', 'Data presensi tidak lengkap. Muat ulang halaman lalu coba lagi.');
            }
        }

        $hitung = ['MASUK' => 0, 'IZIN' => 0, 'SAKIT' => 0, 'ABSEN' => 0];

        try {
            DB::transaction(function () use ($siswas, $presensi, $kartu, $jadwal, $user, $tanggal, &$hitung) {
                $q = DB::table('absensi_utamas')->where('tanggal', $tanggal);

                if (Absen::whereKelas($q, 'kelas', $kartu->kelas)->exists()) {
                    throw new RuntimeException('sudah-diabsen');
                }

                $waktu = now();
                $rows  = [];

                foreach ($siswas as $s) {
                    $hitung[$presensi[$s->id]]++;

                    $rows[] = [
                        'tanggal'      => $tanggal,
                        'kelas'        => $kartu->kelas,
                        'siswa_id'     => $s->id,
                        'status'       => $presensi[$s->id],
                        'user_id'      => $user->id,
                        'jadwal_id'    => $jadwal->id,
                        'is_pengganti' => $kartu->is_pengganti,
                        'created_at'   => $waktu,
                        'updated_at'   => $waktu,
                    ];
                }

                DB::table('absensi_utamas')->insert($rows);
            });
        } catch (RuntimeException $e) {
            if ($e->getMessage() !== 'sudah-diabsen' && ! $e instanceof QueryException) {
                throw $e;
            }

            return $balik()->with('error', "Absensi Utama kelas {$kartu->kelas} pada tanggal tersebut sudah diselesaikan.");
        }

        $tampil = Carbon::parse($tanggal)->format('d/m/Y');

        return $balik()->with(
            'success',
            ($kembaliRekap !== null ? "Absensi Utama susulan {$tampil} " : 'Absensi Utama ')
            . "Kelas {$kartu->kelas} tersimpan dan terkunci: "
            . "Hadir {$hitung['MASUK']}, Izin {$hitung['IZIN']}, Sakit {$hitung['SAKIT']}, Alpa {$hitung['ABSEN']}."
        );
    }

    /** Validasi absen utama susulan: [kartu, tanggal, pesanError]. */
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
            return $gagal('Absen susulan hanya untuk tanggal yang sudah lewat. Untuk hari ini gunakan kartu Absensi Utama di dashboard.');
        }

        if ($libur = Absen::hariLibur($tgl)) {
            return $gagal("Tanggal {$tampil} adalah hari libur ({$libur->keterangan}).");
        }

        $q = DB::table('absensi_utamas')->where('tanggal', $tanggal);

        if (Absen::whereKelas($q, 'kelas', $jadwal->kelas)->exists()) {
            return $gagal("Absensi Utama tanggal {$tampil} sudah diselesaikan.");
        }

        $info = Absen::infoSusulanUtama($jadwal->kelas, $tgl, Absen::guruDari(Auth::user()));

        if (! $info || (int) $info['jadwal_id'] !== (int) $jadwal->id) {
            return $gagal("Tanggal {$tampil} bukan hari sekolah untuk kelas {$jadwal->kelas}.");
        }

        if (! $info['boleh']) {
            return $gagal(
                "Absensi Utama susulan tanggal {$tampil} hanya dapat diisi oleh "
                . ($info['pengganti'] ? 'guru pengganti ' : 'guru jam ke-1 ') . $info['nama'] . '.'
            );
        }

        return [(object) [
            'id'           => $jadwal->id,
            'jadwal_id'    => $jadwal->id,
            'kelas'        => $jadwal->kelas,
            'jam_masuk'    => substr((string) ($jadwal->jam_masuk ?: '00:00'), 0, 5),
            'status'       => 'susulan',
            'is_pengganti' => $info['pengganti'],
            'selesai_oleh' => null,
        ], $tgl, null];
    }

    /** Filter rekap utama yang dipertahankan saat absen susulan. */
    private function filterRekap(Request $request, Jadwal $jadwal): array
    {
        $filter = ['kelas' => $request->query('kelas', $jadwal->kelas)];

        foreach (['dari', 'sampai'] as $k) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query($k))) {
                $filter[$k] = $request->query($k);
            }
        }

        if (ctype_digit((string) $request->query('periode'))) {
            $filter['periode'] = $request->query('periode');
        }

        return $filter;
    }

    private function pesanTolak(?object $kartu): ?string
    {
        return match (true) {
            $kartu === null             => 'Absensi Utama hanya dapat diisi oleh guru yang mengajar di jam ke-1 kelas tersebut hari ini.',
            $kartu->status === 'libur'  => "Hari ini libur ({$kartu->libur}). Absensi tidak dapat dilakukan.",
            $kartu->status === 'belum'  => "Belum waktunya. Absensi Utama dibuka mulai pukul {$kartu->jam_masuk} WIB.",
            default                     => null,
        };
    }
}
