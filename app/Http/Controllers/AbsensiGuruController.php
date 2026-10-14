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
                . ' WIB!';

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
                . ' berhasil disimpan!';
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

            return ['ok' => true, 'lat' => $lat, 'lng' => $lng, 'jarak' => null];
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
                    . ' m, maksimal ' . $akurasiMaks . ' m). Perkiraan jarak Anda dari sekolah ±' . $jarak
                    . ' m. Gunakan HP dengan GPS aktif di area terbuka, lalu coba lagi.',
            ];
        }

        $radius = (int) config('sekolah.radius_meter');

        if ($jarak > $radius) {

            return [
                'ok'    => false,
                'pesan' => 'Anda berada ±' . $jarak . ' m dari sekolah. Presensi hanya dapat dilakukan dalam radius '
                    . $radius . ' m dari lokasi sekolah.',
            ];
        }

        return ['ok' => true, 'lat' => $lat, 'lng' => $lng, 'jarak' => $jarak];
    }


    /**
     * ============================================================
     * DASHBOARD GURU
     *
     * TIDAK ADA LAGI VALIDASI STATUS ABSEN.
     * ============================================================
     */
    public function dashboard()
    {
        $user = Auth::user();

        $today =
            Carbon::now('Asia/Jakarta')->toDateString();


        // ========================================================
        // DATA GURU
        // ========================================================

        $guru = Guru::where(
                'user_id',
                $user->id
            )
            ->orWhere(
                'nama',
                'ILIKE',
                '%' . $user->name . '%'
            )
            ->first();


        // ========================================================
        // MATA PELAJARAN GURU
        // ========================================================

        $mapelGuru =
            $guru->mata_pelajaran
            ?? $guru->mapel
            ?? 'Bahasa Indonesia';


        // ========================================================
        // KELAS YANG DIAJAR GURU
        // ========================================================

        $kelasMengajar = [];


        if ($guru) {

            $val =
                $guru->kelas_mengajar
                ?? $guru->kelas
                ?? '';


            if (
                is_string($val) &&
                !empty($val)
            ) {

                $kelasMengajar =
                    array_map(
                        'trim',
                        explode(',', $val)
                    );

            } elseif (is_array($val)) {

                $kelasMengajar = $val;

            }
        }


        // ========================================================
        // QUERY KELAS
        // ========================================================

        $kelasesQuery =
            class_exists(Kelas::class)
                ? Kelas::query()
                : DB::table('kelases');


        if (!empty($kelasMengajar)) {

            $kelasesQuery->where(
                function ($q) use ($kelasMengajar) {

                    foreach ($kelasMengajar as $km) {

                        $q->orWhere(
                            'nama_kelas',
                            'ILIKE',
                            '%' . $km . '%'
                        );

                    }

                }
            );

        }


        $kelases = $kelasesQuery->get();


        // ========================================================
        // JIKA KELAS BELUM ADA DI TABLE KELASES
        // ========================================================

        if (
            $kelases->isEmpty() &&
            !empty($kelasMengajar)
        ) {

            $kelases = collect();


            foreach ($kelasMengajar as $km) {

                $kelases->push(
                    (object) [
                        'nama_kelas' => $km
                    ]
                );

            }

        }


        // ========================================================
        // CEK COLUMN JAM ABSENSI SISWA
        // ========================================================

        $hasJamMasukInAbsensi =
            Schema::hasColumn(
                'absensi_siswas',
                'jam_masuk'
            );


        $hasJamSelesaiInAbsensi =
            Schema::hasColumn(
                'absensi_siswas',
                'jam_selesai'
            );


        // ========================================================
        // DATA KELAS UNTUK DASHBOARD
        // ========================================================

        $kelasesWithCount =
            $kelases->map(
                function ($kelas) use (
                    $user,
                    $guru,
                    $today,
                    $hasJamMasukInAbsensi,
                    $hasJamSelesaiInAbsensi
                ) {

                    $namaKelas =
                        $kelas->nama_kelas
                        ?? $kelas;


                    // =================================================
                    // JUMLAH MURID
                    // =================================================

                    $jumlahMurid =
                        DB::table('siswas')
                            ->where(
                                'kelas',
                                'ILIKE',
                                '%' . $namaKelas . '%'
                            )
                            ->count();


                    // =================================================
                    // CARI JADWAL
                    // =================================================

                    $jadwal = null;


                    if (
                        class_exists(Jadwal::class)
                        ||
                        Schema::hasTable('jadwals')
                    ) {

                        $jadwalQuery =
                            DB::table('jadwals')
                                ->where(
                                    'kelas',
                                    'ILIKE',
                                    '%' . $namaKelas . '%'
                                );


                        if ($guru) {

                            $jadwalQuery->where(
                                function ($q) use ($guru) {

                                    $q->where(
                                        'guru_id',
                                        $guru->id
                                    )
                                    ->orWhere(
                                        'nama_guru',
                                        'ILIKE',
                                        '%' . $guru->nama . '%'
                                    );

                                }
                            );

                        }


                        $jadwal =
                            $jadwalQuery->first();

                    }


                    // =================================================
                    // JAM MASUK
                    // =================================================

                    if (
                        $jadwal &&
                        !empty($jadwal->jam_masuk)
                    ) {

                        $jamMasuk =
                            substr(
                                $jadwal->jam_masuk,
                                0,
                                5
                            );

                    } else {

                        $jamMasuk = '07:00';

                    }


                    // =================================================
                    // JAM PULANG
                    // =================================================

                    if (
                        $jadwal &&
                        !empty($jadwal->jam_selesai)
                    ) {

                        $jamPulang =
                            substr(
                                $jadwal->jam_selesai,
                                0,
                                5
                            );

                    } else {

                        $jamPulang = '13:00';

                    }


                    // =================================================
                    // DATA YANG DIKIRIM KE VIEW
                    //
                    // TIDAK ADA:
                    // $absensiTerbaru
                    // $status_validasi
                    // =================================================

                    $kelas->nama_kelas =
                        $namaKelas;

                    $kelas->jumlah_murid =
                        $jumlahMurid;

                    $kelas->jam_masuk =
                        $jamMasuk;

                    $kelas->jam_pulang =
                        $jamPulang;


                    return $kelas;

                }
            );


        return view(
            'guru.dashboard',
            compact(
                'guru',
                'mapelGuru',
                'kelasesWithCount'
            )
        );
    }


    /**
     * ============================================================
     * FORM ABSEN SISWA
     * ============================================================
     */
    public function formAbsenSiswa(Request $request)
    {
        $user = Auth::user();


        $guru = Guru::where(
                'user_id',
                $user->id
            )
            ->orWhere(
                'nama',
                'ILIKE',
                '%' . $user->name . '%'
            )
            ->first();


        $kelasMengajar = [];


        $defaultMapel =
            $guru->mata_pelajaran
            ?? $guru->mapel
            ?? $request->query(
                'mapel',
                'Bahasa Indonesia'
            );


        if ($guru) {

            $val =
                $guru->kelas_mengajar
                ?? $guru->kelas
                ?? '';


            if (
                is_string($val) &&
                !empty($val)
            ) {

                $kelasMengajar =
                    array_map(
                        'trim',
                        explode(',', $val)
                    );

            } elseif (is_array($val)) {

                $kelasMengajar = $val;

            }

        }


        $selectedKelasUrl =
            $request->query('kelas', '');


        if ($request->filled('mapel')) {

            $defaultMapel =
                $request->query('mapel');

        }


        // ========================================================
        // MAPEL
        // ========================================================

        $mapels =
            class_exists(Mapel::class)
                ? Mapel::all()
                : collect();


        // ========================================================
        // KELAS
        // ========================================================

        $kelasesQuery =
            class_exists(Kelas::class)
                ? Kelas::query()
                : DB::table('kelases');


        if (!empty($kelasMengajar)) {

            $kelasesQuery->where(
                function ($q) use ($kelasMengajar) {

                    foreach ($kelasMengajar as $km) {

                        $q->orWhere(
                            'nama_kelas',
                            'ILIKE',
                            '%' . $km . '%'
                        );

                    }

                }
            );

        }


        $kelases =
            $kelasesQuery->get();


        // ========================================================
        // JADWAL
        // ========================================================

        $jadwalsQuery =
            DB::table('jadwals');


        if ($guru) {

            $jadwalsQuery->where(
                function ($q) use ($guru) {

                    $q->where(
                        'guru_id',
                        $guru->id
                    )
                    ->orWhere(
                        'nama_guru',
                        'ILIKE',
                        '%' . $guru->nama . '%'
                    );

                }
            );

        }


        $jadwalList =
            $jadwalsQuery->get();


        $defaultJamMasuk = '07:00';

        $defaultJamSelesai = '08:30';


        // ========================================================
        // CARI JADWAL KELAS YANG DIPILIH
        // ========================================================

        if (!empty($selectedKelasUrl)) {

            $cleanUrlKelas =
                strtolower(
                    trim(
                        str_replace(
                            [
                                'kelas',
                                ' '
                            ],
                            '',
                            $selectedKelasUrl
                        )
                    )
                );


            $jadwalMatch =
                $jadwalList->first(
                    function ($j) use ($cleanUrlKelas) {

                        $cleanJadwalKelas =
                            strtolower(
                                trim(
                                    str_replace(
                                        [
                                            'kelas',
                                            ' '
                                        ],
                                        '',
                                        $j->kelas ?? ''
                                    )
                                )
                            );


                        return
                            $cleanJadwalKelas === $cleanUrlKelas
                            ||
                            str_contains(
                                $cleanJadwalKelas,
                                $cleanUrlKelas
                            )
                            ||
                            str_contains(
                                $cleanUrlKelas,
                                $cleanJadwalKelas
                            );

                    }
                );


            if ($jadwalMatch) {

                if (!empty($jadwalMatch->jam_masuk)) {

                    $defaultJamMasuk =
                        date(
                            'H:i',
                            strtotime(
                                $jadwalMatch->jam_masuk
                            )
                        );

                }


                if (!empty($jadwalMatch->jam_selesai)) {

                    $defaultJamSelesai =
                        date(
                            'H:i',
                            strtotime(
                                $jadwalMatch->jam_selesai
                            )
                        );

                }

            }

        }


        return view(
            'guru.absen_siswa',
            compact(
                'mapels',
                'kelases',
                'defaultMapel',
                'selectedKelasUrl',
                'jadwalList',
                'defaultJamMasuk',
                'defaultJamSelesai'
            )
        );
    }


    /**
     * ============================================================
     * SIMPAN SESI
     * ============================================================
     */
    public function simpanSesi(Request $request)
    {
        $request->validate([
            'pembelajaran' => 'required',
            'kelas'        => 'required',
            'pertemuan_ke' => 'required',
            'tanggal'      => 'required',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
        ]);


        session([
            'sesi_mapel' =>
                $request->pembelajaran,

            'sesi_kelas' =>
                $request->kelas,

            'sesi_pertemuan' =>
                $request->pertemuan_ke,

            'sesi_tanggal' =>
                $request->tanggal,

            'sesi_jam_mulai' =>
                $request->jam_mulai,

            'sesi_jam_selesai' =>
                $request->jam_selesai,

            'sesi_materi' =>
                $request->materi ?? '',
        ]);


        return redirect()
            ->route('guru.absen.siswa.dashboard')
            ->with(
                'success',
                'Sesi pembelajaran dibuka, silakan absen murid!'
            );
    }


    /**
     * ============================================================
     * DASHBOARD ABSENSI SISWA
     * ============================================================
     */
    public function dashboardAbsenSiswa()
    {
        $kelasSesi =
            session('sesi_kelas');


        if (class_exists(Siswa::class)) {

            $query =
                Siswa::query();


            if ($kelasSesi) {

                $query->where(
                    'kelas',
                    'ILIKE',
                    '%' . $kelasSesi . '%'
                );

            }


            $siswas =
                $query->get();

        } else {

            $siswas =
                collect();

        }


        return view(
            'guru.dashboard_absen_siswa',
            compact('siswas')
        );
    }


    /**
     * ============================================================
     * SIMPAN SELURUH ABSENSI SISWA
     * ============================================================
     */
    public function simpanAbsenSiswaBatch(Request $request)
    {
        $user = Auth::user();


        $presensiData =
            $request->input(
                'presensi',
                []
            );


        $mapel =
            session(
                'sesi_mapel',
                'Mata Pelajaran'
            );


        $kelas =
            session(
                'sesi_kelas',
                '-'
            );


        $pertemuan =
            session(
                'sesi_pertemuan',
                '1'
            );


        $tanggal =
            session(
                'sesi_tanggal',
                Carbon::now('Asia/Jakarta')
                    ->toDateString()
            );


        $jamMulai =
            session(
                'sesi_jam_mulai',
                '07:00'
            );


        $jamSelesai =
            session(
                'sesi_jam_selesai',
                '13:00'
            );


        $hasJamMasuk =
            Schema::hasColumn(
                'absensi_siswas',
                'jam_masuk'
            );


        $hasJamSelesai =
            Schema::hasColumn(
                'absensi_siswas',
                'jam_selesai'
            );


        $totalSiswa =
            count($presensiData);


        foreach (
            $presensiData
            as $siswaId => $status
        ) {

            $updateValues = [

                'kelas' =>
                    $kelas,

                'status' =>
                    $status,

                'created_at' =>
                    now(),

                'updated_at' =>
                    now(),

            ];


            if ($hasJamMasuk) {

                $updateValues['jam_masuk'] =
                    $jamMulai;

            }


            if ($hasJamSelesai) {

                $updateValues['jam_selesai'] =
                    $jamSelesai;

            }


            DB::table('absensi_siswas')
                ->updateOrInsert(

                    [
                        'user_id' =>
                            $user->id,

                        'siswa_id' =>
                            $siswaId,

                        'tanggal' =>
                            $tanggal,

                        'mapel' =>
                            $mapel,

                        'pertemuan_ke' =>
                            $pertemuan,
                    ],

                    $updateValues

                );

        }


        return redirect()
            ->route(
                'guru.rekap-siswa',
                [
                    'kelas' => $kelas
                ]
            )
            ->with([

                'swal_success' => true,

                'swal_title' =>
                    'Presensi Berhasil Disimpan',

                'swal_message' =>
                    "Data presensi siswa Kelas {$kelas} "
                    . "untuk mata pelajaran {$mapel} "
                    . "(Pertemuan ke-{$pertemuan}) "
                    . "sebanyak {$totalSiswa} siswa "
                    . "telah berhasil dicatat ke dalam sistem.",

                'swal_kelas' =>
                    $kelas,

                'swal_mapel' =>
                    $mapel,

                'success' =>
                    "Presensi Kelas {$kelas} "
                    . "berhasil disimpan ke database!"

            ]);
    }


    /**
     * ============================================================
     * REKAP ABSEN SISWA
     * ============================================================
     */
    public function rekapSiswaIndex(Request $request)
    {
        $user = Auth::user();


        $guru = Guru::where(
                'user_id',
                $user->id
            )
            ->orWhere(
                'nama',
                'ILIKE',
                '%' . $user->name . '%'
            )
            ->first();


        $kelasMengajar = [];


        if ($guru) {

            $val =
                $guru->kelas_mengajar
                ?? $guru->kelas
                ?? '';


            if (
                is_string($val) &&
                !empty($val)
            ) {

                $kelasMengajar =
                    array_map(
                        'trim',
                        explode(',', $val)
                    );

            } elseif (is_array($val)) {

                $kelasMengajar = $val;

            }

        }


        $selectedKelas =
            $request->input('kelas');


        $selectedBulan =
            $request->input(
                'bulan',
                date('m')
            );


        $selectedTahun =
            $request->input(
                'tahun',
                date('Y')
            );


        $kelasesQuery =
            class_exists(Kelas::class)
                ? Kelas::query()
                : DB::table('kelases');


        if (!empty($kelasMengajar)) {

            $kelasesQuery->where(
                function ($q) use ($kelasMengajar) {

                    foreach ($kelasMengajar as $km) {

                        $q->orWhere(
                            'nama_kelas',
                            'ILIKE',
                            '%' . $km . '%'
                        );

                    }

                }
            );

        }


        $kelases =
            $kelasesQuery->get();


        $querySiswa =
            class_exists(Siswa::class)
                ? Siswa::query()
                : DB::table('siswas');


        if ($selectedKelas) {

            $querySiswa->where(
                'kelas',
                'ILIKE',
                '%' . $selectedKelas . '%'
            );

        } elseif (!empty($kelasMengajar)) {

            $querySiswa->where(
                function ($q) use ($kelasMengajar) {

                    foreach ($kelasMengajar as $km) {

                        $q->orWhere(
                            'kelas',
                            'ILIKE',
                            '%' . $km . '%'
                        );

                    }

                }
            );

        }


        $siswas =
            $querySiswa->get();


        $rekapData = [];


        foreach ($siswas as $siswa) {

            $absensiQuery =
                DB::table('absensi_siswas')
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'siswa_id',
                        $siswa->id
                    )
                    ->whereMonth(
                        'tanggal',
                        $selectedBulan
                    )
                    ->whereYear(
                        'tanggal',
                        $selectedTahun
                    );


            $hadir =
                (clone $absensiQuery)
                    ->where(
                        'status',
                        'MASUK'
                    )
                    ->count();


            $sakit =
                (clone $absensiQuery)
                    ->where(
                        'status',
                        'SAKIT'
                    )
                    ->count();


            $izin =
                (clone $absensiQuery)
                    ->where(
                        'status',
                        'IZIN'
                    )
                    ->count();


            $alpa =
                (clone $absensiQuery)
                    ->where(
                        'status',
                        'ABSEN'
                    )
                    ->count();


            $rekapData[] =
                (object) [

                    'id' =>
                        $siswa->id,

                    'nama' =>
                        $siswa->nama
                        ?? $siswa->name,

                    'kelas' =>
                        $siswa->kelas,

                    'hadir' =>
                        $hadir,

                    'sakit' =>
                        $sakit,

                    'izin' =>
                        $izin,

                    'alpa' =>
                        $alpa,

                ];

        }


        return view(
            'guru.rekap_siswa',
            compact(
                'rekapData',
                'kelases',
                'selectedKelas',
                'selectedBulan',
                'selectedTahun'
            )
        );
    }


    /**
     * ============================================================
     * HAPUS REKAP PRESENSI SISWA
     * ============================================================
     */
    public function hapusRekapSiswa(
        Request $request,
        $siswa
    ) {

        $user = Auth::user();


        $kelas =
            $request->input('kelas');


        $bulan =
            $request->input(
                'bulan',
                date('m')
            );


        $tahun =
            $request->input(
                'tahun',
                date('Y')
            );


        $deleted =
            DB::table('absensi_siswas')
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'siswa_id',
                    $siswa
                )
                ->whereMonth(
                    'tanggal',
                    $bulan
                )
                ->whereYear(
                    'tanggal',
                    $tahun
                )
                ->delete();


        if ($deleted > 0) {

            $message =
                'Rekap presensi siswa berhasil dihapus.';

        } else {

            $message =
                'Tidak ada data presensi yang ditemukan '
                . 'untuk dihapus pada periode ini.';

        }


        return redirect()
            ->route(
                'guru.rekap-siswa',
                [
                    'kelas' =>
                        $kelas,

                    'bulan' =>
                        $bulan,

                    'tahun' =>
                        $tahun,
                ]
            )
            ->with(
                'success',
                $message
            );
    }
}