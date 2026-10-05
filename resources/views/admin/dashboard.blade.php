<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SMP Djojoredjo</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            teal: '#24B9A1',
                            dark: '#0C2320',
                            lightTeal: '#32D2B9',
                            bg: '#EAF0F6'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #24B9A1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <!-- OVERLAY BACKGROUND UNTUK SIDEBAR HP -->
    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        
        <!-- SIDEBAR -->
        @include('layouts.partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            
            <!-- TOPBAR HEADER -->
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">DASHBOARD ADMIN</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60 flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-[#24B9A1]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="p-2 bg-white text-[#24B9A1] rounded-lg shadow-sm border border-slate-200/60 hover:bg-slate-50 transition" title="Refresh Dashboard">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 space-y-6">
                
                <!-- WELCOME BANNER -->
                <div class="relative bg-gradient-to-r from-[#24B9A1] to-[#1DA38D] rounded-3xl p-6 sm:p-8 text-white shadow-xl overflow-hidden flex justify-between items-center">
                    <div class="z-10 max-w-xl">
                        <p class="text-xs font-medium text-white/80 mb-1">Selamat Datang, Administrator</p>
                        <h2 class="text-xl sm:text-2xl font-bold tracking-tight mb-2 sm:mb-3">{{ Auth::user()->name ?? 'Admin Sekolah' }}</h2>
                        <p class="text-[11px] sm:text-xs text-white/90 leading-relaxed font-light">
                            Kelola data guru, siswa, rekap presensi, dan pencatatan keuangan SPP SMP Djojoredjo dengan cepat dan terstruktur.
                        </p>
                    </div>
                    <div class="w-24 h-24 sm:w-32 sm:h-32 opacity-20 rounded-full bg-white/30 flex items-center justify-center p-3 flex-shrink-0 -mr-4 hidden sm:flex">
                        <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}" 
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=SMP+D&background=24B9A1&color=fff';" 
                             alt="Watermark Logo" class="w-full h-full object-cover rounded-full">
                    </div>
                </div>

                <!-- TOP STATS CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Card 1: Data Sekolah -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex flex-col justify-between">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-3 bg-teal-50 text-[#24B9A1] rounded-2xl">
                                <i class="fa-solid fa-school text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Data Sekolah</h3>
                                <p class="text-[11px] text-slate-400">Ringkasan entitas aktif dari database</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-medium text-slate-500">Guru</p>
                                <p class="text-base font-bold text-[#24B9A1] mt-0.5">{{ $totalGuru ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-medium text-slate-500">Siswa</p>
                                <p class="text-base font-bold text-[#24B9A1] mt-0.5">{{ $totalSiswa ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-medium text-slate-500">Kelas</p>
                                <p class="text-base font-bold text-[#24B9A1] mt-0.5">{{ $totalKelas ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-medium text-slate-500">Mapel</p>
                                <p class="text-base font-bold text-[#24B9A1] mt-0.5">{{ $totalMapel ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Absensi Guru Hari Ini -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex flex-col justify-between">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-3 bg-teal-50 text-[#24B9A1] rounded-2xl">
                                <i class="fa-solid fa-clipboard-user text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">Absensi Guru Hari Ini</h3>
                                <p class="text-[11px] text-slate-400">Status kehadiran pengajar hari ini</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="bg-emerald-50/80 p-2.5 rounded-2xl border border-emerald-100">
                                <p class="text-[10px] font-medium text-emerald-700">Hadir</p>
                                <p class="text-base font-bold text-emerald-600 mt-0.5">{{ $hadirGuru ?? 0 }}</p>
                            </div>
                            <div class="bg-amber-50/80 p-2.5 rounded-2xl border border-amber-100">
                                <p class="text-[10px] font-medium text-amber-700">Izin</p>
                                <p class="text-base font-bold text-amber-600 mt-0.5">{{ $izinGuru ?? 0 }}</p>
                            </div>
                            <div class="bg-rose-50/80 p-2.5 rounded-2xl border border-rose-100">
                                <p class="text-[10px] font-medium text-rose-700">Sakit</p>
                                <p class="text-base font-bold text-rose-600 mt-0.5">{{ $sakitGuru ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-100 p-2.5 rounded-2xl border border-slate-200">
                                <p class="text-[10px] font-medium text-slate-600">Alpa</p>
                                <p class="text-base font-bold text-slate-600 mt-0.5">{{ $alpaGuru ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: SPP Bulan Ini -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl flex flex-col justify-between">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="p-3 bg-teal-50 text-[#24B9A1] rounded-2xl">
                                <i class="fa-solid fa-money-check-dollar text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-sm">SPP (Bulan Ini)</h3>
                                <p class="text-[11px] text-slate-400">Keuangan periode berjalan</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-emerald-50/80 p-2.5 rounded-2xl border border-emerald-100">
                                <p class="text-[10px] font-medium text-emerald-700">Lunas</p>
                                <p class="text-base font-bold text-emerald-600 mt-0.5">{{ $lunasSpp ?? 0 }}</p>
                            </div>
                            <div class="bg-rose-50/80 p-2.5 rounded-2xl border border-rose-100">
                                <p class="text-[10px] font-medium text-rose-700">Belum</p>
                                <p class="text-base font-bold text-rose-600 mt-0.5">{{ $belumBayarSpp ?? 0 }}</p>
                            </div>
                            <div class="bg-blue-50/80 p-2.5 rounded-2xl border border-blue-100 flex flex-col justify-center">
                                <p class="text-[10px] font-medium text-blue-700">Pemasukan</p>
                                <p class="text-xs font-bold text-blue-600 mt-0.5">Rp {{ number_format($totalPemasukanSpp ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- MIDDLE TABLES AREA -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Table Absensi Guru Terbaru (Dari Database) -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-user-clock text-[#24B9A1]"></i> Absensi Guru Terbaru
                            </h3>
                            <a href="{{ route('admin.absen') }}" class="text-[11px] font-semibold text-[#24B9A1] hover:underline">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                                        <th class="p-3">NO</th>
                                        <th class="p-3">NAMA GURU</th>
                                        <th class="p-3">MASUK</th>
                                        <th class="p-3">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($absensiGuruTerbaru ?? [] as $index => $absen)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="p-3 font-semibold">{{ $index + 1 }}</td>
                                            <td class="p-3 font-bold text-slate-800">{{ $absen->nama_guru }}</td>
                                            <td class="p-3 font-medium text-slate-700">{{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) : '-' }}</td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                                    {{ $absen->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-400 italic">Belum ada data presensi guru hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Table Pembayaran SPP Terbaru (Dari Database) -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-[#24B9A1]"></i> Pembayaran SPP Terbaru
                            </h3>
                            <a href="{{ route('admin.spp.index') }}" class="text-[11px] font-semibold text-[#24B9A1] hover:underline">Lihat Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                                        <th class="p-3">NO</th>
                                        <th class="p-3">NAMA SISWA</th>
                                        <th class="p-3">JUMLAH</th>
                                        <th class="p-3">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($sppTerbaru ?? [] as $index => $spp)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="p-3 font-semibold">{{ $index + 1 }}</td>
                                            <td class="p-3 font-bold text-slate-800">{{ $spp->siswa->nama ?? 'Siswa Tidak Ditemukan' }}</td>
                                            <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($spp->nominal, 0, ',', '.') }}</td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $spp->status == 'Lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                    {{ $spp->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-6 text-center text-slate-400 italic">Belum ada data pembayaran SPP tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>

</body>
</html>