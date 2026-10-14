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

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PRESENSI GURU</h1>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 flex items-center justify-center min-h-[calc(100vh-80px)]">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 w-full max-w-3xl">
                    <h2 class="text-2xl font-bold text-center text-slate-900 mb-6">Presensi Guru</h2>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- STATUS PRESENSI HARI INI -->
                    <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row justify-around items-center gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-600">Status Hari Ini:</span>
                            @if(optional($absensiHariIni)->status)
                                <span class="px-3 py-1 rounded-full font-bold 
                                    {{ $absensiHariIni->status == 'Hadir' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $absensiHariIni->status == 'Izin' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $absensiHariIni->status == 'Sakit' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ $absensiHariIni->status }} 
                                    @if($absensiHariIni->jam_masuk)
                                        ({{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} WIB)
                                    @endif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full font-bold bg-slate-200 text-slate-600">Belum Mengisi Presensi</span>
                            @endif
                        </div>
                    </div>

                    <!-- FORM PRESENSI -->
                    <form id="form_presensi" action="{{ route('guru.absen.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="akurasi" id="akurasi">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Nama / NUPTK Guru</label>
                                <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full font-semibold p-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-700">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Waktu Sekarang (WIB)</label>
                                <input type="text" id="waktu_otomatis" readonly class="w-full font-bold p-3 bg-teal-50 border border-teal-200 rounded-xl text-[#24B9A1]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Pilih Status Kehadiran</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Hadir" class="peer hidden" checked>
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-[#24B9A1] peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Hadir</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Izin" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-amber-500 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Izin</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Sakit" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-rose-500 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Sakit</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="jenis_presensi" value="Pulang" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-blue-600 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Pulang</span>
                                </label>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">*Pilih "Pulang" jika ingin melakukan absensi jam kepulangan.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Keterangan / Alasan (Opsional untuk Izin/Sakit)</label>
                            <textarea name="keterangan" rows="3" placeholder="Masukkan keterangan jika izin atau sakit..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]"></textarea>
                        </div>

                        <!-- STATUS LOKASI GPS -->
                        <div id="kotak_lokasi" class="p-4 rounded-2xl border bg-slate-50 border-slate-200 text-xs flex items-start gap-3">
                            <i id="ikon_lokasi" class="fa-solid fa-location-crosshairs text-slate-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p id="teks_lokasi" class="font-semibold text-slate-600">Mencari lokasi Anda...</p>
                                <p id="detail_lokasi" class="text-[11px] text-slate-400 mt-0.5">Izinkan akses lokasi pada browser. Lokasi diperlukan untuk presensi Hadir &amp; Pulang (radius {{ config('sekolah.radius_meter') }} m dari sekolah).</p>
                            </div>
                            <button type="button" id="btn_ulang_lokasi" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[11px] font-semibold text-slate-600 hover:bg-slate-100 transition">
                                <i class="fa-solid fa-rotate"></i> Perbarui
                            </button>
                        </div>

                        <button type="submit" id="btn_kirim" class="w-full py-3.5 bg-[#24B9A1] hover:bg-[#1DA38D] text-white font-semibold rounded-2xl text-xs sm:text-sm shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Kirim Presensi
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Konfigurasi lokasi sekolah (dibaca oleh JavaScript di bawah) -->
    <div id="gps-config" class="hidden"
         data-lat="{{ config('sekolah.latitude') }}"
         data-lng="{{ config('sekolah.longitude') }}"
         data-radius="{{ (int) config('sekolah.radius_meter') }}"
         data-akurasi="{{ (int) config('sekolah.akurasi_maks_meter') }}"
         data-wajib="{{ config('sekolah.gps_wajib') ? '1' : '0' }}"></div>

    <script>
        // ===== JAM OTOMATIS =====
        setInterval(() => {
            const now = new Date();
            document.getElementById('waktu_otomatis').value = now.toLocaleTimeString('id-ID', { timeZone: 'Asia/Jakarta' }) + ' WIB';
        }, 1000);

        // ===== LOKASI GPS =====
        // Catatan: pengecekan di sini hanya untuk kenyamanan pengguna.
        // Validasi yang sebenarnya (dan tidak bisa dilewati) dilakukan di server.
        const cfg = document.getElementById('gps-config').dataset;
        const SEKOLAH = {
            lat: cfg.lat === '' ? null : parseFloat(cfg.lat),
            lng: cfg.lng === '' ? null : parseFloat(cfg.lng),
            radius: parseInt(cfg.radius, 10),
            akurasi: parseInt(cfg.akurasi, 10),
            wajib: cfg.wajib === '1',
        };

        const form = document.getElementById('form_presensi');
        const btnKirim = document.getElementById('btn_kirim');
        const kotak = document.getElementById('kotak_lokasi');
        const ikon = document.getElementById('ikon_lokasi');
        const teks = document.getElementById('teks_lokasi');
        const detail = document.getElementById('detail_lokasi');
        let lokasiOk = false;
        let watchId = null;

        function hitungJarak(lat1, lng1, lat2, lng2) {
            const R = 6371000, rad = d => d * Math.PI / 180;
            const dLat = rad(lat2 - lat1), dLng = rad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function perluLokasi() {
            const pulang = form.querySelector('input[name="jenis_presensi"]:checked');
            const status = form.querySelector('input[name="status"]:checked');
            return !!pulang || (status ? status.value === 'Hadir' : true);
        }

        function tampil(warna, kelasIkon, judul, ket) {
            const tema = {
                abu:   ['bg-slate-50 border-slate-200', 'text-slate-400', 'text-slate-600'],
                hijau: ['bg-emerald-50 border-emerald-200', 'text-emerald-600', 'text-emerald-700'],
                merah: ['bg-rose-50 border-rose-200', 'text-rose-600', 'text-rose-700'],
            }[warna];
            kotak.className = 'p-4 rounded-2xl border text-xs flex items-start gap-3 ' + tema[0];
            ikon.className = 'fa-solid ' + kelasIkon + ' mt-0.5 ' + tema[1];
            teks.className = 'font-semibold ' + tema[2];
            teks.textContent = judul;
            detail.textContent = ket;
        }

        function perbaruiTombol() {
            if (!SEKOLAH.wajib || !perluLokasi()) {
                btnKirim.disabled = false;
                return;
            }
            btnKirim.disabled = !lokasiOk;
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
            const info = '(Posisi terdeteksi: ' + latitude.toFixed(5) + ', ' + longitude.toFixed(5) + ')';

            if (accuracy > SEKOLAH.akurasi) {
                lokasiOk = false;
                tampil('merah', 'fa-signal', 'Akurasi GPS terlalu rendah (±' + akurasi + ' m, maksimal ' + SEKOLAH.akurasi + ' m)', 'Perkiraan jarak ke sekolah ±' + jarak + ' m. Perangkat tanpa chip GPS (laptop/PC) biasanya akurasinya rendah. Gunakan HP dengan GPS aktif di area terbuka, lalu tekan Perbarui. ' + info);
            } else if (jarak > SEKOLAH.radius) {
                lokasiOk = false;
                tampil('merah', 'fa-location-dot', 'Anda berada ±' + jarak + ' m dari sekolah', 'Presensi Hadir & Pulang hanya bisa dilakukan dalam radius ' + SEKOLAH.radius + ' m. (Izin / Sakit tidak dibatasi lokasi.) ' + info);
            } else {
                lokasiOk = true;
                tampil('hijau', 'fa-circle-check', 'Lokasi sesuai: ±' + jarak + ' m dari sekolah', 'Akurasi GPS ±' + akurasi + ' m. Anda berada di dalam radius presensi. ' + info);
            }
            perbaruiTombol();
        }

        function galatPosisi(err) {
            lokasiOk = false;
            const pesan = {
                1: ['Akses lokasi ditolak', 'Izinkan akses lokasi untuk situs ini di pengaturan browser, lalu tekan Perbarui.'],
                2: ['Lokasi tidak tersedia', 'Pastikan GPS/Location aktif di perangkat Anda, lalu tekan Perbarui.'],
                3: ['Pencarian lokasi terlalu lama', 'Pindah ke area terbuka lalu tekan Perbarui.'],
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

        form.querySelectorAll('input[type=radio]').forEach(r => r.addEventListener('change', perbaruiTombol));
        document.getElementById('btn_ulang_lokasi').addEventListener('click', mulaiLokasi);
        window.addEventListener('beforeunload', () => { if (watchId !== null) navigator.geolocation.clearWatch(watchId); });
        mulaiLokasi();
    </script>
</body>
</html>