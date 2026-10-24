<?php

namespace App\Http\Controllers;

use App\Models\AbsensiGuru;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\Geofence;
use App\Services\AbsenSiswaService;
use Carbon\Carbon;

class AbsensiGuruController extends Controller
{
    /**
     * ============================================================
     * HALAMAN REKAP ABSENSI GURU KHUSUS ADMIN
     * ============================================================
     */
    public function rekapAdminIndex(Request $request)
    {
        $query = AbsensiGuru::query();

        // 1. Filter Pencarian berdasarkan Nama atau NUPTK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_guru', 'ILIKE', '%' . $search . '%')
                  ->orWhere('nuptk_nip', 'ILIKE', '%' . $search . '%');
            });
        }

        // 2. Filter berdasarkan Tanggal (jika diisi, abaikan bulan/tahun)
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } else {
            // 3. Filter berdasarkan Bulan & Tahun
            if ($request->filled('bulan')) {
                $query->whereMonth('tanggal', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->whereYear('tanggal', $request->tahun);
            }
        }

        // Ringkasan jumlah (mengikuti filter yang sedang aktif)
        $hitungStatus = (clone $query)
            ->select('status', DB::raw('COUNT(*) AS total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $ringkasan = [
            'total' => (int) $hitungStatus->sum(),
            'Hadir' => (int) ($hitungStatus['Hadir'] ?? 0),
            'Izin'  => (int) ($hitungStatus['Izin'] ?? 0),
            'Sakit' => (int) ($hitungStatus['Sakit'] ?? 0),
        ];

        // Data terbaru
        $absensis = $query
            ->latest('tanggal')
            ->latest('created_at')
            ->paginate(10);

        // Pertahankan filter ketika pagination
        $absensis->appends($request->all());

        // NUPTK cadangan: jika catatan presensi tidak menyimpan NUPTK ("-"),
        // ambil dari tabel data guru berdasarkan akun yang absen.
        $nuptkGuru = Guru::whereIn('user_id', $absensis->pluck('user_id')->filter()->unique())
            ->pluck('nuptk', 'user_id');

        // Daftar guru yang BELUM presensi pada tanggal yang dilihat
        // (tanggal dipilih, atau hari ini jika tidak ada filter bulan/tahun/pencarian)
        $tanggalCek = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal, 'Asia/Jakarta')->toDateString()
            : Carbon::now('Asia/Jakarta')->toDateString();

        $tampilBelumAbsen = $request->filled('tanggal')
            || (! $request->filled('bulan') && ! $request->filled('tahun') && ! $request->filled('search'));

        $belumAbsen = collect();

        if ($tampilBelumAbsen) {
            $sudahAbsen = AbsensiGuru::whereDate('tanggal', $tanggalCek)->pluck('user_id');

            $belumAbsen = User::whereRaw('LOWER(role) = ?', ['guru'])
                ->whereNotIn('id', $sudahAbsen)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('admin.absen.index', compact(
            'absensis', 'ringkasan', 'nuptkGuru', 'belumAbsen', 'tampilBelumAbsen', 'tanggalCek'
        ));
    }


    /**
     * ============================================================
     * TAMPILAN PRESENSI SAYA - GURU
     * ============================================================
     */
    public function presensiGuruView()
    {
        $user = Auth::user();

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $absensiHariIni = AbsensiGuru::where('user_id', $user->id)
            ->where('tanggal', $today)
            ->first();

        return view(
            'guru.absen',
            compact('user', 'absensiHariIni')
        );
    }


    /**
     * ============================================================
     * STORE PRESENSI GURU
     * Hadir, Izin, Sakit, Pulang
     * ============================================================
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        $currentTime = Carbon::now('Asia/Jakarta')->toTimeString();


        $request->validate([
            'status' => 'nullable|in:Hadir,Izin,Sakit',

            'jenis_presensi' => 'nullable|in:Pulang',

            'keterangan' => 'nullable|string|max:500',

            // Data GPS dari browser
            'latitude' => 'nullable|numeric|between:-90,90',

            'longitude' => 'nullable|numeric|between:-180,180',

            'akurasi' => 'nullable|numeric|min:0',
        ]);


        // ========================================================
        // CEK LOKASI GPS (Hadir & Pulang wajib di dalam radius sekolah)
        // Izin / Sakit tidak dibatasi lokasi.
        // ========================================================

        $perluLokasi =
            $request->jenis_presensi === 'Pulang'
            || ($request->status ?? 'Hadir') === 'Hadir';

        $lokasi = null;

        if ($perluLokasi) {

            $lokasi = $this->cekLokasi($request);

            if (! $lokasi['ok']) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $lokasi['pesan']);
            }
        }


        $guruData = Guru::where('user_id', $user->id)
            ->orWhere(
                'nama',
                'ILIKE',
                '%' . $user->name . '%'
            )
            ->first();


        $nuptkNip =
            $guruData->nuptk
            ?? $guruData->nip
            ?? $user->username
            ?? '-';


        $absensi = AbsensiGuru::firstOrNew([
            'user_id' => $user->id,

            'tanggal' => $today,
        ]);


        $absensi->nama_guru = $user->name;

        $absensi->nuptk_nip = $nuptkNip;


        // ========================================================
        // ABSEN PULANG
        // ========================================================

        if ($request->jenis_presensi === 'Pulang') {

            if ($absensi->jam_keluar) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Anda sudah melakukan absen pulang hari ini.'
                    );
            }


            $absensi->jam_keluar = $currentTime;


            $absensi->lat_keluar = $lokasi['lat'];

            $absensi->lng_keluar = $lokasi['lng'];

            $absensi->jarak_keluar = $lokasi['jarak'];


            $pesan =
                'Absen pulang berhasil dicatat pada pukul '
                . substr($currentTime, 0, 5)
                . ' WIB!'
                . $this->infoJarak($lokasi);

        }

        // ========================================================
        // ABSEN MASUK
        // ========================================================
        else {

            $statusPilihan =
                $request->status ?? 'Hadir';


            if (
                $absensi->exists &&
                $absensi->status === 'Hadir' &&
                $absensi->jam_masuk
            ) {

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Anda sudah melakukan presensi masuk hari ini.'
                    );
            }


            $absensi->status = $statusPilihan;


            if ($statusPilihan === 'Hadir') {

                $absensi->jam_masuk = $currentTime;

                $absensi->lat_masuk = $lokasi['lat'];

                $absensi->lng_masuk = $lokasi['lng'];

                $absensi->jarak_masuk = $lokasi['jarak'];

            } else {

                $absensi->jam_masuk = null;

            }


            $pesan =
                'Presensi status '
                . $statusPilihan
                . ' berhasil disimpan!'
                . ($statusPilihan === 'Hadir' ? $this->infoJarak($lokasi) : '');
        }


        if ($request->filled('keterangan')) {

            $absensi->keterangan =
                $request->keterangan;

        }


        $absensi->save();


        return redirect()
            ->back()
            ->with('success', $pesan);
    }


    /** Kalimat tambahan "Jarak Anda dari sekolah: ..." untuk pesan sukses presensi. */
    private function infoJarak(?array $lokasi): string
    {
        if (! $lokasi || ! isset($lokasi['jarak']) || $lokasi['jarak'] === null) {
            return '';
        }

        return ' Jarak Anda dari sekolah: ' . Geofence::format($lokasi['jarak'])
            . ' (radius maksimal ' . Geofence::format((int) config('sekolah.radius_meter')) . ').';
    }


    /**
     * ============================================================
     * VALIDASI LOKASI GPS (SERVER-SIDE)
     * Hitung jarak guru ke titik sekolah dengan rumus Haversine.
     * ============================================================
     *
     * @return array{ok: bool, pesan?: string, lat?: float|null, lng?: float|null, jarak?: int|null}
     */
    private function cekLokasi(Request $request): array
    {
        $lat = $request->filled('latitude') ? (float) $request->latitude : null;

        $lng = $request->filled('longitude') ? (float) $request->longitude : null;

        // Pembatasan lokasi dimatikan lewat .env (SEKOLAH_GPS_WAJIB=false)
        if (! config('sekolah.gps_wajib')) {

            $jarakOpsional = null;

            if ($lat !== null && $lng !== null
                && config('sekolah.latitude') !== null && config('sekolah.longitude') !== null) {

                $jarakOpsional = (int) round(Geofence::jarakMeter(
                    (float) config('sekolah.latitude'), (float) config('sekolah.longitude'), $lat, $lng
                ));
            }

            return ['ok' => true, 'lat' => $lat, 'lng' => $lng, 'jarak' => $jarakOpsional];
        }

        $sekolahLat = config('sekolah.latitude');

        $sekolahLng = config('sekolah.longitude');

        if ($sekolahLat === null || $sekolahLng === null) {

            return [
                'ok'    => false,
                'pesan' => 'Koordinat sekolah belum dikonfigurasi. Hubungi admin.',
            ];
        }

        if ($lat === null || $lng === null) {

            return [
                'ok'    => false,
                'pesan' => 'Lokasi GPS tidak terdeteksi. Aktifkan GPS & izinkan akses lokasi di browser, lalu coba lagi.',
            ];
        }

        $jarak = (int) round(
            Geofence::jarakMeter((float) $sekolahLat, (float) $sekolahLng, $lat, $lng)
        );

        $akurasiMaks = (int) config('sekolah.akurasi_maks_meter');

        if ($request->filled('akurasi') && (float) $request->akurasi > $akurasiMaks) {

            return [
                'ok'    => false,
                'pesan' => 'Akurasi GPS terlalu rendah (±' . round((float) $request->akurasi)
                    . ' m, maksimal ' . $akurasiMaks . ' m). Perkiraan jarak Anda dari sekolah ±' . Geofence::format($jarak)
                    . '. Gunakan HP dengan GPS aktif di area terbuka, lalu coba lagi.',
            ];
        }

        $radius = (int) config('sekolah.radius_meter');

        if ($jarak > $radius) {

            return [
                'ok'    => false,
                'pesan' => 'Anda berada ±' . Geofence::format($jarak) . ' dari sekolah. Presensi hanya dapat dilakukan dalam radius '
                    . Geofence::format($radius) . ' dari lokasi sekolah.',
            ];
        }

        return ['ok' => true, 'lat' => $lat, 'lng' => $lng, 'jarak' => $jarak];
    }


    /**
     * ============================================================
     * DASHBOARD GURU
     *
     * Menampilkan kartu jadwal HARI INI saja (jadwal sendiri +
     * jadwal yang menugaskan guru ini sebagai pengganti).
     * Dari kartu inilah absen siswa dimulai (akses satu pintu).
     * ============================================================
     */
    public function dashboard()
    {
        $user = Auth::user();
        $now  = AbsenSiswaService::sekarang();

        $guru          = AbsenSiswaService::guruDari($user);
        $jadwalHariIni = AbsenSiswaService::jadwalHariIni($user, $now);
        $libur         = AbsenSiswaService::hariLibur($now);

        return view('guru.dashboard', compact('guru', 'jadwalHariIni', 'libur', 'now'));
    }
}