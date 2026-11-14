@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">
@php
    $hariIni      = \Carbon\Carbon::now('Asia/Jakarta');
    $sudahMasuk   = (bool) optional($absensiHariIni)->jam_masuk;
    $sudahPulang  = (bool) optional($absensiHariIni)->jam_keluar;
    $lengkap      = $sudahMasuk && $sudahPulang;
    $terkunciIzin = (bool) optional($absensiHariIni)->terkunci;
    $statusHari   = optional($absensiHariIni)->status;
    $radiusTeks   = \App\Support\Geofence::format((int) config('sekolah.radius_meter'));

    // Aturan Jendela Waktu Presensi (WIB)
    $bukaMasuk    = $hariIni->copy()->setTime(6, 0, 0);
    $batasTelat   = $hariIni->copy()->setTime(7, 20, 0);
    $lockMasuk    = $hariIni->copy()->setTime(8, 30, 0);

    $bukaPulang   = $hariIni->copy()->setTime(13, 35, 0);
    $lockPulang   = $hariIni->copy()->setTime(16, 30, 0);

    $bisaMasukWaktu  = $hariIni->between($bukaMasuk, $lockMasuk);
    $bisaPulangWaktu = $hariIni->between($bukaPulang, $lockPulang);
    $sedangTelat     = $hariIni->gt($batasTelat) && $hariIni->lte($lockMasuk);

    $infoWaktuMasuk = match (true) {
        $sudahMasuk                 => 'Sudah dicatat pukul ' . \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i'),
        $hariIni->lt($bukaMasuk)    => 'Terkunci — Dibuka pukul 06:00 WIB',
        $hariIni->gt($lockMasuk)    => 'Terkunci — Melewati batas 08:30 WIB',
        $sedangTelat                => 'Dibuka (Terlambat > 07:20 WIB)',
        default                     => 'Dibuka (06:00 - 07:20 Tepat Waktu)',
    };

    $infoWaktuPulang = match (true) {
        $sudahPulang                => 'Sudah dicatat pukul ' . \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i'),
        ! $sudahMasuk               => 'Tersedia setelah presensi masuk',
        $hariIni->lt($bukaPulang)   => 'Terkunci — Baru dibuka pukul 13:35 WIB',
        $hariIni->gt($lockPulang)   => 'Terkunci — Melewati batas 16:30 WIB',
        default                     => 'Dibuka (13:35 - 16:30 WIB)',
    };

    $masukTersedia  = ! $sudahMasuk && $bisaMasukWaktu;
    $pulangTersedia = $sudahMasuk && ! $sudahPulang && $bisaPulangWaktu;
    $aksiTerkunci   = ! $masukTersedia && ! $pulangTersedia;

    $defaultAksi = $pulangTersedia ? 'pulang' : ($masukTersedia ? 'masuk' : '');
@endphp

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center gap-3">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PRESENSI GURU</h1>
                </div>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ Absen::tanggalPanjang($hariIni) }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 pt-2 flex justify-center">
                <div class="w-full max-w-3xl space-y-5">

                    <!-- KARTU PROFIL + JAM -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/70 font-semibold">Presensi Guru</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">{{ Auth::user()->name }}</h2>
                            <p class="text-xs text-white/80 mt-1">SMP Djojoredjo • Jam Masuk 06:00–08:30 (Toleransi 07:20) • Pulang 13:35–16:30</p>
                        </div>
                        <div class="text-left sm:text-right">
                            <p class="text-[11px] uppercase tracking-widest text-white/70 font-semibold">Waktu Sekarang</p>
                            <p id="waktu_otomatis" class="text-2xl sm:text-3xl font-extrabold tabular-nums">--:--:--</p>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-2xl flex items-start gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5"></i><span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl flex items-start gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5"></i><span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- RINGKASAN HARI INI -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-slate-900">Ringkasan Hari Ini</h3>
                            @if($statusHari)
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold
                                    {{ $statusHari === 'Hadir' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $statusHari === 'Izin' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $statusHari === 'Sakit' ? 'bg-rose-100 text-rose-700' : '' }}">{{ $statusHari }}</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-600">Belum Presensi</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                <p class="text-slate-500 font-semibold"><i class="fa-solid fa-right-to-bracket text-emerald-500 mr-1"></i>Jam Masuk</p>
                                <p class="text-lg font-extrabold text-slate-800 mt-1">{{ $sudahMasuk ? \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') : '--:--' }}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                <p class="text-slate-500 font-semibold"><i class="fa-solid fa-right-from-bracket text-blue-500 mr-1"></i>Jam Pulang</p>
                                <p class="text-lg font-extrabold text-slate-800 mt-1">{{ $sudahPulang ? \Carbon\Carbon::parse($absensiHariIni->jam_keluar)->format('H:i') : '--:--' }}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                <p class="text-slate-500 font-semibold"><i class="fa-solid fa-location-dot text-emerald-500 mr-1"></i>Jarak Masuk</p>
                                <p class="text-lg font-extrabold text-slate-800 mt-1">{{ optional($absensiHariIni)->jarak_masuk !== null ? \App\Support\Geofence::format((int) $absensiHariIni->jarak_masuk) : '-' }}</p>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                <p class="text-slate-500 font-semibold"><i class="fa-solid fa-location-dot text-blue-500 mr-1"></i>Jarak Pulang</p>
                                <p class="text-lg font-extrabold text-slate-800 mt-1">{{ optional($absensiHariIni)->jarak_keluar !== null ? \App\Support\Geofence::format((int) $absensiHariIni->jarak_keluar) : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($terkunciIzin)
                        <div class="p-4 bg-sky-50 border border-sky-200 rounded-2xl text-sky-900 text-xs font-semibold flex items-start gap-3">
                            <i class="fa-solid fa-lock text-sky-600 text-base mt-0.5"></i>
                            <span>Presensi hari ini terkunci: izin <b>{{ $statusHari }}</b> Anda sudah disetujui admin. {{ $absensiHariIni->keterangan }}</span>
                        </div>
                    @endif

                    <!-- FORM PRESENSI -->
                    <form id="form_presensi" action="{{ route('guru.absen.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="akurasi" id="akurasi">
                        <input type="hidden" name="status" id="in_status" value="Hadir" disabled>
                        <input type="hidden" name="jenis_presensi" id="in_jenis" value="Pulang" disabled>

                        <fieldset class="space-y-5 {{ $terkunciIzin ? 'opacity-50' : '' }}" {{ $terkunciIzin ? 'disabled' : '' }}>

                            <!-- PILIH AKSI -->
                            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <h3 class="text-sm font-bold text-slate-900">Pilih Presensi</h3>
                                    <span class="text-[11px] font-semibold text-slate-500">
                                        Masuk: <b>06:00–08:30</b> (Batas Telat 07:20) • Pulang: <b>13:35–16:30</b>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- KARTU PRESENSI MASUK -->
                                    <label id="card_masuk" class="{{ $masukTersedia ? 'cursor-pointer' : 'cursor-not-allowed opacity-65' }}">
                                        <input type="radio" name="aksi" id="radio_masuk" value="masuk" class="peer hidden"
                                               {{ $defaultAksi === 'masuk' ? 'checked' : '' }}
                                               {{ ! $masukTersedia ? 'disabled' : '' }}>
                                        <span class="flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 transition peer-checked:border-[#19A389] peer-checked:bg-emerald-50">
                                            <span id="icon_box_masuk" class="w-11 h-11 rounded-xl {{ $masukTersedia ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-lg">
                                                <i id="icon_masuk" class="fa-solid {{ $masukTersedia ? 'fa-right-to-bracket' : ($sudahMasuk ? 'fa-circle-check' : 'fa-lock') }}"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-bold text-slate-800">Presensi Masuk</span>
                                                <span id="sub_masuk" class="block text-[11px] {{ $sedangTelat && ! $sudahMasuk ? 'text-amber-600 font-bold' : 'text-slate-500' }}">{{ $infoWaktuMasuk }}</span>
                                            </span>
                                        </span>
                                    </label>

                                    <!-- KARTU PRESENSI PULANG -->
                                    <label id="card_pulang" class="{{ $pulangTersedia ? 'cursor-pointer' : 'cursor-not-allowed opacity-65' }}">
                                        <input type="radio" name="aksi" id="radio_pulang" value="pulang" class="peer hidden"
                                               {{ $defaultAksi === 'pulang' ? 'checked' : '' }}
                                               {{ ! $pulangTersedia ? 'disabled' : '' }}>
                                        <span class="flex items-center gap-3 p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 transition peer-checked:border-blue-600 peer-checked:bg-blue-50">
                                            <span id="icon_box_pulang" class="w-11 h-11 rounded-xl {{ $pulangTersedia ? 'bg-blue-100 text-blue-600' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-lg">
                                                <i id="icon_pulang" class="fa-solid {{ $pulangTersedia ? 'fa-right-from-bracket' : ($sudahPulang ? 'fa-circle-check' : 'fa-lock') }}"></i>
                                            </span>
                                            <span>
                                                <span class="block text-sm font-bold text-slate-800">Presensi Pulang</span>
                                                <span id="sub_pulang" class="block text-[11px] text-slate-500">{{ $infoWaktuPulang }}</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                <p class="text-[11px] text-slate-500 mt-3">
                                    <i class="fa-solid fa-circle-info text-sky-500 mr-1"></i>
                                    Tidak masuk karena sakit atau dinas? Ajukan melalui menu
                                    <a href="{{ route('guru.izin.index') }}" class="font-bold text-[#138A73] hover:underline">Pengajuan Izin</a>
                                    (dengan bukti, menunggu persetujuan admin).
                                </p>
                            </div>

                            <!-- LOKASI -->
                            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-sm font-bold text-slate-900">Verifikasi Lokasi</h3>
                                    <button type="button" id="btn_ulang_lokasi" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-[11px] font-semibold text-slate-600 transition">
                                        <i class="fa-solid fa-rotate mr-1"></i> Perbarui Lokasi
                                    </button>
                                </div>

                                <div id="kotak_lokasi" class="p-4 rounded-2xl border bg-slate-50 border-slate-200 text-xs flex items-start gap-3">
                                    <i id="ikon_lokasi" class="fa-solid fa-location-crosshairs text-slate-400 text-lg mt-0.5"></i>
                                    <div class="flex-1">
                                        <p id="teks_lokasi" class="font-bold text-slate-600 text-sm">Mencari lokasi Anda...</p>
                                        <p id="detail_lokasi" class="text-[11px] text-slate-500 mt-1 leading-relaxed">Izinkan akses lokasi pada browser. Presensi hanya dapat dilakukan dalam radius {{ $radiusTeks }} dari sekolah.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3 mt-4 text-center text-xs">
                                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                        <p class="text-[10px] uppercase tracking-wide font-bold text-slate-400">Jarak Anda</p>
                                        <p id="t_jarak" class="text-base sm:text-lg font-extrabold text-slate-800 mt-1">-</p>
                                    </div>
                                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                        <p class="text-[10px] uppercase tracking-wide font-bold text-slate-400">Batas Radius</p>
                                        <p class="text-base sm:text-lg font-extrabold text-slate-800 mt-1">{{ $radiusTeks }}</p>
                                    </div>
                                    <div id="t_kurang_box" class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                                        <p id="t_kurang_label" class="text-[10px] uppercase tracking-wide font-bold text-slate-400">Selisih</p>
                                        <p id="t_kurang" class="text-base sm:text-lg font-extrabold text-slate-800 mt-1">-</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="flex justify-between text-[10px] font-semibold text-slate-400 mb-1">
                                        <span>Posisi Anda</span><span>Titik sekolah</span>
                                    </div>
                                    <div class="h-2.5 rounded-full bg-slate-200 overflow-hidden">
                                        <div id="bar_jarak" class="h-full rounded-full bg-slate-300 transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>

                                <p id="t_akurasi" class="mt-3 text-[11px] text-slate-500"></p>
                            </div>

                            <!-- CATATAN -->
                            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                                <label class="block text-sm font-bold text-slate-900 mb-2">Catatan <span class="font-normal text-slate-400 text-xs">(opsional)</span></label>
                                <textarea name="keterangan" rows="2" placeholder="Mis. terlambat karena ..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]"></textarea>
                            </div>

                            <button type="submit" id="btn_kirim" {{ ($lengkap || $aksiTerkunci) ? 'disabled' : '' }}
                                    class="w-full py-4 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-2xl text-sm shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                                @if($lengkap)
                                    <i class="fa-solid fa-circle-check mr-1"></i> Presensi Hari Ini Sudah Lengkap
                                @elseif($aksiTerkunci)
                                    <i class="fa-solid fa-lock mr-1"></i> <span id="label_kirim">Presensi Terkunci di Luar Jam Jadwal</span>
                                @else
                                    <i class="fa-solid fa-paper-plane mr-1"></i> <span id="label_kirim">{{ $defaultAksi === 'pulang' ? 'Kirim Presensi Pulang' : 'Kirim Presensi Masuk' }}</span>
                                @endif
                            </button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Konfigurasi lokasi & jendela waktu sekolah -->
    <div id="gps-config" class="hidden"
         data-lat="{{ config('sekolah.latitude') }}"
         data-lng="{{ config('sekolah.longitude') }}"
         data-radius="{{ (int) config('sekolah.radius_meter') }}"
         data-akurasi="{{ (int) config('sekolah.akurasi_maks_meter') }}"
         data-wajib="{{ config('sekolah.gps_wajib') ? '1' : '0' }}"
         data-lengkap="{{ $lengkap ? '1' : '0' }}"
         data-sudah-masuk="{{ $sudahMasuk ? '1' : '0' }}"
         data-sudah-pulang="{{ $sudahPulang ? '1' : '0' }}"
         data-terkunci-izin="{{ $terkunciIzin ? '1' : '0' }}"></div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        const cfg = document.getElementById('gps-config').dataset;
        const SEKOLAH = {
            lat: cfg.lat === '' ? null : parseFloat(cfg.lat),
            lng: cfg.lng === '' ? null : parseFloat(cfg.lng),
            radius: parseInt(cfg.radius, 10),
            akurasi: parseInt(cfg.akurasi, 10),
            wajib: cfg.wajib === '1',
            lengkap: cfg.lengkap === '1',
            sudahMasuk: cfg.sudahMasuk === '1',
            sudahPulang: cfg.sudahPulang === '1',
            terkunciIzin: cfg.terkunciIzin === '1',
        };

        const form = document.getElementById('form_presensi');
        const btnKirim = document.getElementById('btn_kirim');
        const kotak = document.getElementById('kotak_lokasi');
        const ikon = document.getElementById('ikon_lokasi');
        const teks = document.getElementById('teks_lokasi');
        const detail = document.getElementById('detail_lokasi');
        const tJarak = document.getElementById('t_jarak');
        const tKurang = document.getElementById('t_kurang');
        const tKurangLabel = document.getElementById('t_kurang_label');
        const tKurangBox = document.getElementById('t_kurang_box');
        const tAkurasi = document.getElementById('t_akurasi');
        const bar = document.getElementById('bar_jarak');
        let lokasiOk = false;
        let watchId = null;

        // Ambil waktu Jakarta (WIB) saat ini dalam detik sejak 00:00:00
        function getDetikWIB() {
            const str = new Date().toLocaleTimeString('en-GB', { timeZone: 'Asia/Jakarta', hour12: false });
            const [j, m, d] = str.split(':').map(Number);
            return { teks: str, detik: j * 3600 + m * 60 + d };
        }

        function cekStatusWaktu() {
            const { teks: jamTeks, detik } = getDetikWIB();
            document.getElementById('waktu_otomatis').textContent = jamTeks;

            // Jendela waktu dalam detik:
            // Masuk: 06:00 (21600) - 08:30 (30600), toleransi telat: 07:20 (26400)
            // Pulang: 13:35 (48900) - 16:30 (59400)
            const bukaMasuk  = 6 * 3600;
            const batasTelat = 7 * 3600 + 20 * 60;
            const lockMasuk  = 8 * 3600 + 30 * 60;
            const bukaPulang = 13 * 3600 + 35 * 60;
            const lockPulang = 16 * 3600 + 30 * 60;

            const bisaMasuk  = !SEKOLAH.sudahMasuk && !SEKOLAH.terkunciIzin && detik >= bukaMasuk && detik <= lockMasuk;
            const bisaPulang = SEKOLAH.sudahMasuk && !SEKOLAH.sudahPulang && !SEKOLAH.terkunciIzin && detik >= bukaPulang && detik <= lockPulang;

            const radioMasuk  = document.getElementById('radio_masuk');
            const radioPulang = document.getElementById('radio_pulang');
            const cardMasuk   = document.getElementById('card_masuk');
            const cardPulang  = document.getElementById('card_pulang');
            const subMasuk    = document.getElementById('sub_masuk');
            const subPulang   = document.getElementById('sub_pulang');

            if (radioMasuk && !SEKOLAH.sudahMasuk) {
                radioMasuk.disabled = !bisaMasuk;
                cardMasuk.className = bisaMasuk ? 'cursor-pointer' : 'cursor-not-allowed opacity-65';
                if (bisaMasuk && !radioMasuk.checked && !bisaPulang) radioMasuk.checked = true;
                if (!bisaMasuk) radioMasuk.checked = false;

                if (detik < bukaMasuk) subMasuk.textContent = 'Terkunci — Dibuka pukul 06:00 WIB';
                else if (detik > lockMasuk) subMasuk.textContent = 'Terkunci — Melewati batas 08:30 WIB';
                else if (detik > batasTelat) subMasuk.textContent = 'Dibuka (Terlambat > 07:20 WIB)';
                else subMasuk.textContent = 'Dibuka (06:00 - 07:20 Tepat Waktu)';
            }

            if (radioPulang && SEKOLAH.sudahMasuk && !SEKOLAH.sudahPulang) {
                radioPulang.disabled = !bisaPulang;
                cardPulang.className = bisaPulang ? 'cursor-pointer' : 'cursor-not-allowed opacity-65';
                if (bisaPulang && !radioPulang.checked) radioPulang.checked = true;
                if (!bisaPulang) radioPulang.checked = false;

                if (detik < bukaPulang) subPulang.textContent = 'Terkunci — Baru dibuka pukul 13:35 WIB';
                else if (detik > lockPulang) subPulang.textContent = 'Terkunci — Melewati batas 16:30 WIB';
                else subPulang.textContent = 'Dibuka (13:35 - 16:30 WIB)';
            }

            return bisaMasuk || bisaPulang;
        }

        function perbaruiTombol() {
            const waktuOk = cekStatusWaktu();
            if (SEKOLAH.lengkap || SEKOLAH.terkunciIzin || !waktuOk) {
                btnKirim.disabled = true;
                return;
            }
            btnKirim.disabled = SEKOLAH.wajib ? !lokasiOk : false;
        }

        cekStatusWaktu();
        setInterval(perbaruiTombol, 1000);

        // ===== LOKASI GPS =====
        function fmtJarak(m) {
            if (m < 1000) return m + ' m';
            return (m / 1000).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' km';
        }

        function hitungJarak(lat1, lng1, lat2, lng2) {
            const R = 6371000, rad = d => d * Math.PI / 180;
            const dLat = rad(lat2 - lat1), dLng = rad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        const TEMA = {
            abu:    { kotak: 'bg-slate-50 border-slate-200',    ikon: 'text-slate-400',   judul: 'text-slate-600',   bar: 'bg-slate-300',   box: 'bg-slate-50 border-slate-200' },
            hijau:  { kotak: 'bg-emerald-50 border-emerald-200', ikon: 'text-emerald-600', judul: 'text-emerald-800', bar: 'bg-emerald-500', box: 'bg-emerald-50 border-emerald-200' },
            kuning: { kotak: 'bg-amber-50 border-amber-200',     ikon: 'text-amber-600',   judul: 'text-amber-800',   bar: 'bg-amber-400',   box: 'bg-amber-50 border-amber-200' },
            merah:  { kotak: 'bg-rose-50 border-rose-200',       ikon: 'text-rose-600',    judul: 'text-rose-800',    bar: 'bg-rose-500',    box: 'bg-rose-50 border-rose-200' },
        };

        function tampil(warna, kelasIkon, judul, ket) {
            const t = TEMA[warna];
            kotak.className = 'p-4 rounded-2xl border text-xs flex items-start gap-3 ' + t.kotak;
            ikon.className = 'fa-solid ' + kelasIkon + ' text-lg mt-0.5 ' + t.ikon;
            teks.className = 'font-bold text-sm ' + t.judul;
            teks.textContent = judul;
            detail.textContent = ket;
            bar.className = 'h-full rounded-full transition-all duration-500 ' + t.bar;
            tKurangBox.className = 'p-3 rounded-2xl border ' + t.box;
        }

        function terimaPosisi(pos) {
            const { latitude, longitude, accuracy } = pos.coords;
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;
            document.getElementById('akurasi').value = accuracy;

            if (SEKOLAH.lat === null || SEKOLAH.lng === null) {
                lokasiOk = false;
                tampil('merah', 'fa-triangle-exclamation', 'Koordinat sekolah belum dikonfigurasi', 'Hubungi admin untuk mengisi SEKOLAH_LAT dan SEKOLAH_LNG.');
                perbaruiTombol();
                return;
            }

            const jarak = Math.round(hitungJarak(SEKOLAH.lat, SEKOLAH.lng, latitude, longitude));
            const akurasi = Math.round(accuracy);
            const dalamRadius = jarak <= SEKOLAH.radius;
            const akurasiBaik = accuracy <= SEKOLAH.akurasi;
            const kurang = jarak - SEKOLAH.radius;

            tJarak.textContent = fmtJarak(jarak);
            if (dalamRadius) {
                tKurangLabel.textContent = 'Status';
                tKurang.textContent = 'Di dalam radius';
            } else {
                tKurangLabel.textContent = 'Kurang';
                tKurang.textContent = fmtJarak(kurang);
            }

            const persen = dalamRadius ? 100 : Math.max(4, Math.min(96, (SEKOLAH.radius / jarak) * 100));
            bar.style.width = persen + '%';

            const koordinat = 'Koordinat terdeteksi: ' + latitude.toFixed(5) + ', ' + longitude.toFixed(5) + '.';

            tAkurasi.innerHTML = '';
            const barisAkurasi = document.createElement('span');
            barisAkurasi.className = akurasiBaik ? 'text-slate-500' : 'text-rose-600 font-semibold';
            barisAkurasi.textContent = 'Akurasi GPS ±' + akurasi + ' m (maksimal ' + SEKOLAH.akurasi + ' m)'
                + (akurasiBaik ? '. ' : ' - terlalu rendah. Gunakan HP dengan GPS aktif di area terbuka, lalu tekan Perbarui Lokasi. ')
                + koordinat;
            tAkurasi.appendChild(barisAkurasi);

            if (!dalamRadius) {
                lokasiOk = false;
                tampil(kurang <= SEKOLAH.radius * 3 ? 'kuning' : 'merah', 'fa-location-dot',
                    'Anda belum berada di area sekolah',
                    'Anda berada ' + fmtJarak(jarak) + ' dari titik sekolah, sedangkan batas presensi ' + fmtJarak(SEKOLAH.radius) + '. '
                    + 'Dekati sekolah sejauh ' + fmtJarak(kurang) + ' lagi agar masuk radius presensi.');
            } else if (!akurasiBaik) {
                lokasiOk = false;
                tampil('kuning', 'fa-signal', 'Akurasi GPS belum memadai',
                    'Posisi Anda ' + fmtJarak(jarak) + ' dari sekolah (di dalam radius), tetapi akurasi GPS ±' + akurasi + ' m melebihi batas ' + SEKOLAH.akurasi + ' m. Tunggu sinyal membaik atau tekan Perbarui Lokasi.');
            } else {
                lokasiOk = true;
                tampil('hijau', 'fa-circle-check', 'Lokasi sesuai',
                    'Anda berada ' + fmtJarak(jarak) + ' dari titik sekolah, di dalam radius presensi ' + fmtJarak(SEKOLAH.radius) + '. Silakan kirim presensi.');
            }

            perbaruiTombol();
        }

        function galatPosisi(err) {
            lokasiOk = false;
            const pesan = {
                1: ['Akses lokasi ditolak', 'Izinkan akses lokasi untuk situs ini di pengaturan browser, lalu tekan Perbarui Lokasi.'],
                2: ['Lokasi tidak tersedia', 'Pastikan GPS/Location aktif di perangkat Anda, lalu tekan Perbarui Lokasi.'],
                3: ['Pencarian lokasi terlalu lama', 'Pindah ke area terbuka lalu tekan Perbarui Lokasi.'],
            }[err.code] || ['Gagal mendapatkan lokasi', 'Coba lagi beberapa saat.'];
            tampil('merah', 'fa-location-pin-lock', pesan[0], pesan[1]);
            perbaruiTombol();
        }

        function mulaiLokasi() {
            if (!navigator.geolocation) {
                tampil('merah', 'fa-triangle-exclamation', 'Browser tidak mendukung GPS', 'Gunakan browser terbaru (Chrome/Edge/Safari). Lokasi hanya berfungsi pada HTTPS atau localhost.');
                perbaruiTombol();
                return;
            }
            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
            lokasiOk = false;
            tampil('abu', 'fa-location-crosshairs', 'Mencari lokasi Anda...', 'Izinkan akses lokasi pada browser.');
            perbaruiTombol();
            watchId = navigator.geolocation.watchPosition(terimaPosisi, galatPosisi, {
                enableHighAccuracy: true, timeout: 20000, maximumAge: 0
            });
        }

        form.addEventListener('submit', function () {
            const aksi = form.querySelector('input[name="aksi"]:checked');
            const status = document.getElementById('in_status');
            const jenis = document.getElementById('in_jenis');
            status.disabled = true;
            jenis.disabled = true;
            if (aksi && aksi.value === 'pulang') { jenis.disabled = false; }
            else { status.disabled = false; }
        });

        form.querySelectorAll('input[name="aksi"]').forEach(function (r) {
            r.addEventListener('change', function () {
                const label = document.getElementById('label_kirim');
                if (label) label.textContent = r.value === 'pulang' ? 'Kirim Presensi Pulang' : 'Kirim Presensi Masuk';
            });
        });

        document.getElementById('btn_ulang_lokasi').addEventListener('click', mulaiLokasi);
        window.addEventListener('beforeunload', () => { if (watchId !== null) navigator.geolocation.clearWatch(watchId); });
        mulaiLokasi();
    </script>
</body>
</html>