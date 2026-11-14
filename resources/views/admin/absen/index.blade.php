<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #19A389;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    @php
        // Tampilkan NUPTK dari catatan presensi; jika kosong/"-", pakai data guru
        $nuptkTampil = function ($absen) use ($nuptkGuru) {
            $nilai = $absen->nuptk_nip;
            if ($nilai && $nilai !== '-') {
                return $nilai;
            }
            return $nuptkGuru[$absen->user_id] ?? '-';
        };
    @endphp

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <!-- TOPBAR HEADER -->
            <header class="bg-[#EAF0F6]/90 backdrop-blur-md sticky top-0 z-20 px-4 sm:px-8 py-3.5 flex justify-between items-center gap-3 border-b border-slate-200/50 lg:border-none">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="mobileSidebarBtn" aria-label="Buka Menu"
                        class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl bg-white shadow-sm border border-slate-200/60 transition shrink-0">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest truncate">
                        PRESENSI • REKAP ABSENSI GURU
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition">
                        <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Laporan Kehadiran Pendidik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Rekap Absensi Guru</h2>
                            <p class="text-xs text-white/80 mt-1">Daftar presensi masuk, pulang, lokasi geofencing, serta status izin/sakit harian guru</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-clipboard-user"></i>
                            <span>{{ $absensis->total() }} Catatan</span>
                        </div>
                    </div>

                    <!-- CARD FILTER PENCARIAN & TANGGAL -->
                    <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-xl border border-slate-100">
                        <form action="{{ route('admin.absen') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                            <!-- Search by NUPTK / Nama -->
                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Cari Guru</label>
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NUPTK..."
                                           class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                            </div>

                            <!-- Filter Bulan -->
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Bulan</label>
                                <select name="bulan"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Semua Bulan --</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Tahun -->
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tahun</label>
                                <select name="tahun"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Semua Tahun --</option>
                                    @php $currentYear = date('Y'); @endphp
                                    @foreach(range($currentYear - 2, $currentYear + 1) as $y)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Tanggal Spesifik -->
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Atau Tanggal Spesifik</label>
                                <input type="date" name="tanggal" value="{{ request('tanggal') }}" title="Pilih Tanggal Spesifik"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <!-- Tombol Aksi Filter -->
                            <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                                <button type="submit"
                                        class="flex-1 py-2.5 px-4 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white rounded-xl text-xs font-extrabold shadow-sm transition inline-flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-filter"></i>
                                    <span>Filter</span>
                                </button>
                                @if(request('tanggal') || request('search') || request('bulan') || request('tahun'))
                                    <a href="{{ route('admin.absen') }}"
                                       class="py-2.5 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition shrink-0" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- RINGKASAN (mengikuti filter yang aktif) -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-md flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Presensi</p>
                                <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $ringkasan['total'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-list-check"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-100 shadow-md flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] sm:text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Hadir</p>
                                <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $ringkasan['Hadir'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-amber-100 shadow-md flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] sm:text-[11px] font-bold text-amber-600 uppercase tracking-wider">Izin</p>
                                <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $ringkasan['Izin'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-envelope-open-text"></i>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-rose-100 shadow-md flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[10px] sm:text-[11px] font-bold text-rose-600 uppercase tracking-wider">Sakit</p>
                                <p class="text-2xl font-extrabold text-rose-700 mt-1">{{ $ringkasan['Sakit'] }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-notes-medical"></i>
                            </div>
                        </div>
                    </div>

                    <!-- GURU YANG BELUM PRESENSI -->
                    @if($tampilBelumAbsen)
                        <div class="p-4 sm:p-5 rounded-3xl border shadow-sm {{ $belumAbsen->isEmpty() ? 'bg-emerald-50/90 border-emerald-200' : 'bg-amber-50/90 border-amber-200' }} text-xs space-y-2.5">
                            <p class="font-bold {{ $belumAbsen->isEmpty() ? 'text-emerald-800' : 'text-amber-800' }} flex items-center gap-2">
                                <i class="fa-solid {{ $belumAbsen->isEmpty() ? 'fa-circle-check text-emerald-600' : 'fa-user-clock text-amber-600' }} text-base"></i>
                                <span>
                                    @if($belumAbsen->isEmpty())
                                        Semua guru sudah melakukan presensi pada {{ \Carbon\Carbon::parse($tanggalCek)->translatedFormat('d F Y') }}.
                                    @else
                                        {{ $belumAbsen->count() }} guru belum presensi pada {{ \Carbon\Carbon::parse($tanggalCek)->translatedFormat('d F Y') }}:
                                    @endif
                                </span>
                            </p>
                            @if($belumAbsen->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($belumAbsen as $g)
                                        <span class="px-3 py-1 bg-white border border-amber-200/90 rounded-full text-slate-700 font-semibold shadow-2xs">{{ $g->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- CARD TABEL & DAFTAR PRESENSI GURU -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Top Bar Auto-Refresh -->
                        <div class="px-4 sm:px-6 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px] text-slate-500 bg-slate-50/50">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-clock text-[#19A389]"></i>
                                <span>Diperbarui <span id="jam_perbarui" class="font-bold text-slate-700">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }}</span> WIB</span>
                            </span>
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none font-semibold text-slate-600">
                                <input type="checkbox" id="auto_refresh" class="w-3.5 h-3.5 rounded accent-[#19A389] cursor-pointer">
                                <span>Perbarui otomatis tiap 30 detik</span>
                            </label>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($absensis as $absen)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-slate-900 truncate">{{ $absen->nama_guru }}</p>
                                            <p class="text-[11px] font-mono font-semibold text-[#138A73] mt-0.5">NUPTK/NIP: {{ $nuptkTampil($absen) }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                <i class="fa-regular fa-calendar mr-1 text-slate-400"></i>{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shrink-0
                                            {{ ($absen->status ?? 'Hadir') == 'Hadir' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ ($absen->status ?? '') == 'Izin' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                            {{ ($absen->status ?? '') == 'Sakit' ? 'bg-rose-100 text-rose-700 border border-rose-200' : '' }}">
                                            {{ $absen->status ?? 'Hadir' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-100 text-xs">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Jam Masuk</span>
                                            <span class="font-bold text-emerald-600">{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') . ' WIB' : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Jam Keluar</span>
                                            <span class="font-bold text-rose-600">{{ $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') . ' WIB' : '-' }}</span>
                                        </div>
                                    </div>

                                    @if($absen->lat_masuk !== null || $absen->lat_keluar !== null)
                                        <div class="flex flex-wrap gap-2 text-[11px] font-semibold">
                                            @if($absen->lat_masuk !== null)
                                                <a href="https://www.google.com/maps?q={{ $absen->lat_masuk }},{{ $absen->lng_masuk }}" target="_blank" rel="noopener"
                                                   class="px-2.5 py-1 bg-emerald-50 text-[#138A73] border border-emerald-200 rounded-lg inline-flex items-center gap-1 hover:bg-emerald-100 transition">
                                                    <i class="fa-solid fa-location-dot"></i> Masuk {{ $absen->jarak_masuk !== null ? \App\Support\Geofence::format((int) $absen->jarak_masuk) : '' }}
                                                </a>
                                            @endif
                                            @if($absen->lat_keluar !== null)
                                                <a href="https://www.google.com/maps?q={{ $absen->lat_keluar }},{{ $absen->lng_keluar }}" target="_blank" rel="noopener"
                                                   class="px-2.5 py-1 bg-blue-50 text-blue-600 border border-blue-200 rounded-lg inline-flex items-center gap-1 hover:bg-blue-100 transition">
                                                    <i class="fa-solid fa-location-dot"></i> Pulang {{ $absen->jarak_keluar !== null ? \App\Support\Geofence::format((int) $absen->jarak_keluar) : '' }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    @if($absen->keterangan)
                                        <p class="text-[11px] text-slate-600 bg-slate-50/70 px-3 py-1.5 rounded-xl">
                                            <span class="font-bold text-slate-500">Ket:</span> {{ $absen->keterangan }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-clipboard text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada data presensi tersimpan pada filter ini.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 w-12 text-center">No</th>
                                        <th class="p-4 whitespace-nowrap">Tanggal</th>
                                        <th class="p-4">Nama Guru</th>
                                        <th class="p-4">NUPTK / NIP</th>
                                        <th class="p-4 text-center whitespace-nowrap">Jam Masuk</th>
                                        <th class="p-4 text-center whitespace-nowrap">Jam Keluar</th>
                                        <th class="p-4 text-center">Status</th>
                                        <th class="p-4">Lokasi</th>
                                        <th class="p-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($absensis as $index => $absen)
                                        <tr class="hover:bg-teal-50/40 transition">
                                            <td class="p-4 text-center font-semibold text-slate-400">{{ $absensis->firstItem() + $index }}</td>
                                            <td class="p-4 font-medium text-slate-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                                            <td class="p-4 font-bold text-slate-800">{{ $absen->nama_guru }}</td>
                                            <td class="p-4 text-[#138A73] font-mono font-semibold">{{ $nuptkTampil($absen) }}</td>
                                            <td class="p-4 text-center font-bold text-emerald-600 whitespace-nowrap">
                                                {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') . ' WIB' : '-' }}
                                            </td>
                                            <td class="p-4 text-center font-bold text-rose-600 whitespace-nowrap">
                                                {{ $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') . ' WIB' : '-' }}
                                            </td>
                                            <td class="p-4 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold
                                                    {{ ($absen->status ?? 'Hadir') == 'Hadir' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                                                    {{ ($absen->status ?? '') == 'Izin' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                                    {{ ($absen->status ?? '') == 'Sakit' ? 'bg-rose-100 text-rose-700 border border-rose-200' : '' }}">
                                                    {{ $absen->status ?? 'Hadir' }}
                                                </span>
                                            </td>
                                            <td class="p-4 whitespace-nowrap">
                                                @if($absen->lat_masuk !== null)
                                                    <a href="https://www.google.com/maps?q={{ $absen->lat_masuk }},{{ $absen->lng_masuk }}" target="_blank" rel="noopener"
                                                       class="text-[#138A73] font-semibold hover:underline" title="Lihat lokasi absen masuk di peta">
                                                        <i class="fa-solid fa-location-dot"></i> Masuk {{ $absen->jarak_masuk !== null ? \App\Support\Geofence::format((int) $absen->jarak_masuk) : '' }}
                                                    </a>
                                                @endif
                                                @if($absen->lat_keluar !== null)
                                                    <a href="https://www.google.com/maps?q={{ $absen->lat_keluar }},{{ $absen->lng_keluar }}" target="_blank" rel="noopener"
                                                       class="text-blue-600 font-semibold hover:underline block mt-0.5" title="Lihat lokasi absen pulang di peta">
                                                        <i class="fa-solid fa-location-dot"></i> Pulang {{ $absen->jarak_keluar !== null ? \App\Support\Geofence::format((int) $absen->jarak_keluar) : '' }}
                                                    </a>
                                                @endif
                                                @if($absen->lat_masuk === null && $absen->lat_keluar === null)
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="p-4">{{ $absen->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="p-8 text-center text-slate-400 italic">
                                                Belum ada data presensi tersimpan pada filter ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($absensis->hasPages())
                            <div class="p-4 sm:px-6 border-t border-slate-100">
                                {{ $absensis->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        // Penyegaran otomatis: halaman dimuat ulang tiap 30 detik agar presensi guru yang baru
        // masuk langsung tampil. Tidak memuat ulang saat admin sedang mengetik di kolom filter.
        (function () {
            var kotak = document.getElementById('auto_refresh');
            var KUNCI = 'rekapGuruAutoRefresh';
            var timer = null;

            function simpan(nilai) {
                try { localStorage.setItem(KUNCI, nilai ? '1' : '0'); } catch (e) {}
            }
            function baca() {
                try { return localStorage.getItem(KUNCI) === '1'; } catch (e) { return false; }
            }
            function mulai() {
                timer = setInterval(function () {
                    var aktif = document.activeElement;
                    var sedangMengetik = aktif && (aktif.tagName === 'INPUT' || aktif.tagName === 'SELECT' || aktif.tagName === 'TEXTAREA') && aktif !== kotak;
                    if (!sedangMengetik) { window.location.reload(); }
                }, 30000);
            }
            function berhenti() {
                if (timer) { clearInterval(timer); timer = null; }
            }

            kotak.checked = baca();
            if (kotak.checked) { mulai(); }

            kotak.addEventListener('change', function () {
                simpan(kotak.checked);
                if (kotak.checked) { mulai(); } else { berhenti(); }
            });
        })();
    </script>
</body>
</html>