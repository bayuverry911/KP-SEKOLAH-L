<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SMP Djojoredjo</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            teal: '#19A389',
                            dark: '#0D6353',
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
            background: #19A389;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
</head>

<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <!-- OVERLAY SIDEBAR MOBILE -->
    <div id="mobileOverlay"
         onclick="toggleSidebar()"
         class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity">
    </div>

    <div class="flex min-h-screen relative">

        <!-- ========================================================= -->
        <!-- SIDEBAR -->
        <!-- ========================================================= -->

        <aside id="sidebar"
               class="fixed lg:static top-0 left-0 z-50 w-64
                      bg-gradient-to-b from-[#19A389] via-[#138A73] to-[#0D6353]
                      text-white flex flex-col justify-between py-6 px-0
                      flex-shrink-0 h-screen lg:h-auto min-h-screen
                      transition-transform duration-300 -translate-x-full
                      lg:translate-x-0 overflow-y-auto
                      rounded-tr-3xl rounded-br-3xl shadow-xl">

            <div>

                <!-- LOGO -->
                <div class="flex flex-col items-center text-center pt-2 pb-4 px-4">

                    <div class="w-20 h-20 flex items-center justify-center mb-2">
                        <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=SMP+D&background=19A389&color=fff';"
                             alt="Logo SMP Djojoredjo"
                             class="w-full h-full object-contain rounded-full drop-shadow-md">
                    </div>

                    <span class="text-xs font-extrabold tracking-wider text-white uppercase">
                        SMP DJOJOREDJO
                    </span>

                    <span class="text-[10px] text-emerald-200 uppercase font-semibold tracking-wide">
                        {{ Auth::user()->role ?? 'GURU' }}
                    </span>

                </div>

                <!-- USER INFO -->
                <div class="mx-4 flex items-center space-x-3 p-2.5
                            bg-white/10 border border-white/20 rounded-2xl mb-5
                            shadow-inner backdrop-blur-sm">

                    <div class="w-9 h-9 rounded-full bg-white/20
                                flex items-center justify-center
                                text-white font-bold text-xs flex-shrink-0">
                        <i class="fa-solid fa-user text-sm"></i>
                    </div>

                    <div class="overflow-hidden">
                        <h2 class="font-bold text-xs text-white truncate">
                            {{ Auth::user()->name ?? 'Guru' }}
                        </h2>

                        <p class="text-[10px] text-emerald-200 uppercase font-medium">
                            {{ Auth::user()->role ?? 'GURU' }}
                        </p>
                    </div>

                </div>

                <!-- NAVIGATION -->
                <nav class="space-y-4 text-xs font-medium">

                    <!-- MENU UTAMA -->
                    <div class="px-6 py-1 text-emerald-200/90
                                font-bold text-[11px] uppercase tracking-wider">
                        MENU UTAMA
                    </div>

                    <div class="pl-4">
                        <a href="{{ Route::has('guru.dashboard') ? route('guru.dashboard') : '#' }}"
                           class="w-full transition-all flex items-center px-4 py-2.5
                                  font-bold text-xs shadow-md rounded-l-full
                                  rounded-r-none bg-white text-[#138A73]">

                            <i class="fa-solid fa-house w-4 mr-2"></i>
                            Tampilan Utama

                        </a>
                    </div>

                    <!-- INFORMASI GURU -->
                    <div class="space-y-1">

                        <div class="flex items-center space-x-2 px-6 py-1
                                    text-emerald-200/90 font-bold text-[11px]
                                    uppercase tracking-wider">

                            <i class="fa-regular fa-circle-user w-4 text-emerald-300"></i>
                            <span>INFORMASI GURU</span>

                        </div>

                        <ul class="pl-4 space-y-1 text-white/90">

                            <li>
                                <a href="{{ Route::has('guru.absen') ? route('guru.absen') : '#' }}"
                                   class="hover:text-white flex items-center gap-2
                                          px-4 py-2 transition-all
                                          rounded-l-full rounded-r-none
                                          {{ request()->routeIs('guru.absen')
                                              ? 'bg-white text-[#138A73] font-bold shadow'
                                              : 'hover:bg-white/10 rounded-r-lg' }}">

                                    <span class="text-sm font-bold">•</span>
                                    Presensi Saya

                                </a>
                            </li>
                            <li>
                                <a href="{{ Route::has('guru.izin.index') ? route('guru.izin.index') : '#' }}"
                                   class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('guru.izin.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.izin.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Pengajuan Izin
                                </a>
                            </li>

                        </ul>

                    </div>

                    <!-- RUANG KELAS -->
                    <div class="space-y-1">

                        <div class="flex items-center space-x-2 px-6 py-1
                                    text-emerald-200/90 font-bold text-[11px]
                                    uppercase tracking-wider">

                            <i class="fa-solid fa-chalkboard-user w-4 text-emerald-300"></i>
                            <span>RUANG KELAS</span>

                        </div>

                        <ul class="pl-4 space-y-1 text-white/90">

                            <li>
                                <a href="{{ Route::has('guru.rekap-siswa') ? route('guru.rekap-siswa') : '#' }}"
                                   class="hover:text-white flex items-center gap-2
                                          px-4 py-2 transition-all
                                          rounded-l-full rounded-r-none
                                          {{ request()->routeIs('guru.rekap-siswa')
                                              ? 'bg-white text-[#138A73] font-bold shadow'
                                              : 'hover:bg-white/10 rounded-r-lg' }}">

                                    <span class="text-sm font-bold">•</span>
                                    Rekap Absen Siswa

                                </a>
                            </li>

                            <li>
                                <a href="{{ Route::has('guru.rekap-utama') ? route('guru.rekap-utama') : '#' }}"
                                   class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none
                                          {{ request()->routeIs('guru.rekap-utama') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                    <span class="text-sm font-bold">•</span>
                                    Rekap Absensi Utama
                                </a>
                            </li>

                        </ul>

                    </div>

                    <!-- PENGATURAN -->
                    <div class="space-y-1 pt-3 border-t border-white/20">

                        <div class="flex items-center space-x-2 px-6 py-1
                                    text-emerald-200/90 font-bold text-[11px]
                                    uppercase tracking-wider">

                            <i class="fa-solid fa-gear w-4 text-emerald-300"></i>
                            <span>PENGATURAN</span>

                        </div>

                        <ul class="pl-4 space-y-1 text-white/90">

                            <li>
                                <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}"
                                   class="hover:text-white flex items-center gap-2
                                          px-4 py-2 transition-all
                                          rounded-l-full rounded-r-none
                                          {{ request()->routeIs('profile.*')
                                              ? 'bg-white text-[#138A73] font-bold shadow'
                                              : 'hover:bg-white/10 rounded-r-lg' }}">

                                    <span class="text-sm font-bold">•</span>
                                    Profil Saya

                                </a>
                            </li>

                        </ul>

                    </div>

                </nav>

                <!-- LOGOUT -->
                <div class="px-4 pt-3 mt-4 border-t border-white/20">

                    <form action="{{ Route::has('logout') ? route('logout') : '#' }}"
                          method="POST"
                          id="logoutForm">

                        @csrf

                        <button type="submit"
                                data-confirm="Apakah Anda yakin ingin keluar dari akun ini?"
                                class="w-full text-left px-3 py-2 text-xs font-semibold
                                       text-rose-200 hover:text-white
                                       hover:bg-rose-600/30 rounded-lg
                                       flex items-center gap-2.5 transition-all">

                            <i class="fa-solid fa-right-from-bracket
                                      text-sm text-rose-300"></i>

                            <span>Keluar / Logout</span>

                        </button>

                    </form>

                </div>

            </div>

            <!-- MASCOT -->
            <div class="pt-4 relative flex justify-center items-end mt-auto px-4">

                <img src="{{ asset('images/guru_cewe.png') }}"
                     onerror="this.onerror=null; this.src='https://cdni.iconscout.com/illustration/premium/thumb/female-teacher-holding-book-and-pointing-finger-illustration-download-in-svg-png-gif-file-formats--woman-pointing-up-character-pack-people-illustrations-3798680.png';"
                     alt="Mascot Teacher"
                     class="w-36 max-h-44 h-auto object-contain drop-shadow-lg">

            </div>

        </aside>


        <!-- ========================================================= -->
        <!-- MAIN CONTENT -->
        <!-- ========================================================= -->

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">

            <!-- TOPBAR -->
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2
                           flex justify-between items-center">

                <div class="flex items-center gap-3">

                    <button onclick="toggleSidebar()"
                            class="lg:hidden text-slate-600 p-2 rounded-lg
                                   bg-white shadow-sm hover:bg-slate-100">

                        <i class="fa-solid fa-bars text-lg"></i>

                    </button>

                    <h1 class="text-xs font-bold text-slate-400
                               uppercase tracking-widest">
                        DASHBOARD GURU
                    </h1>

                </div>

                <div class="flex items-center space-x-3">

                    <button onclick="toggleCalendarPanel()"
                            title="Tampilkan / Sembunyikan Kalender & Pengingat"
                            class="text-slate-500 hover:text-slate-800
                                   p-2 rounded-lg bg-white/50 hover:bg-white
                                   shadow-sm transition active:scale-95 flex items-center gap-1.5 text-xs font-bold">

                        <i class="fa-regular fa-calendar text-[#19A389] lg:hidden"></i>
                        <span class="lg:hidden text-[11px] text-slate-600">Kalender</span>
                        <i class="fa-solid fa-ellipsis-vertical text-base sm:text-xl"></i>

                    </button>

                </div>

            </header>


            <!-- BODY -->
            <div class="px-4 sm:px-8 pb-8 space-y-6">

                <!-- NOTIFIKASI TOAST (setelah simpan absen, tetap di dashboard) -->
                @if(session('success') || session('error'))
                    <div id="toastData" class="hidden"
                         data-tipe="{{ session('success') ? 'success' : 'error' }}"
                         data-pesan="{{ session('success') ?? session('error') }}"></div>
                @endif


                <!-- WELCOME BANNER -->
                <div class="relative bg-gradient-to-r from-[#19A389] to-[#138A73]
                            rounded-2xl p-5 sm:p-6 text-white shadow-md
                            overflow-hidden flex justify-between items-center">

                    <div class="z-10 max-w-xl">

                        <p class="text-xs font-medium text-white/80 mb-1">
                            Selamat Datang Kembali,
                        </p>

                        <h2 class="text-xl sm:text-2xl font-bold
                                   tracking-tight mb-2 sm:mb-3">

                            {{ Auth::user()->name ?? 'Guru SMP Djojoredjo' }}

                        </h2>

                        <p class="text-[10px] sm:text-[11px]
                                 text-white/80 leading-relaxed">

                            Jl. Beringin No.45, Pamulang Barat.,
                            Kec. Pamulang, Kota Tangerang Selatan,
                            Banten 15417.

                        </p>

                    </div>

                    <div class="w-24 h-24 sm:w-32 sm:h-32 opacity-20
                                rounded-full bg-white/30 flex items-center
                                justify-center p-3 flex-shrink-0 -mr-4">

                        <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=SMP+D&background=19A389&color=fff';"
                             alt="Watermark Logo"
                             class="w-full h-full object-cover rounded-full">

                    </div>

                </div>


                <!-- DATA & VARIABEL BANTU DASHBOARD -->
                @php
                    $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    $hariIniNama = match((int) ($now->dayOfWeek ?? now()->dayOfWeek)) {
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        default => 'Minggu',
                    };

                    $userLogin     = Auth::user();
                    $namaGuruLogin = trim((string) ($userLogin->name ?? ''));
                    $userIdLogin   = $userLogin->id ?? null;

                    $guruIdLogin = $userLogin->guru_id ?? null;
                    if (! $guruIdLogin && $userIdLogin && class_exists(\App\Models\Guru::class)) {
                        $guruRecord = \App\Models\Guru::where('user_id', $userIdLogin)->first();
                        $guruIdLogin = $guruRecord->id ?? null;
                    }

                    $semuaJadwalAdmin = isset($semuaJadwal)
                        ? collect($semuaJadwal)
                        : \App\Models\Jadwal::query()->get();

                    $mapNamaUser = \App\Models\User::pluck('name', 'id');
                    $mapNamaGuru = class_exists(\App\Models\Guru::class)
                        ? \App\Models\Guru::pluck('nama', 'id')
                        : collect();

                    $urutanHari = array_flip($daftarHari);
                    $semuaJadwalAdmin = $semuaJadwalAdmin->sortBy(function ($item) use ($urutanHari) {
                        $h = ucfirst(strtolower(trim((string) ($item->hari ?? ''))));
                        return ($urutanHari[$h] ?? 99) . '-' . ($item->jam_masuk ?? '') . '-' . ($item->kelas ?? '');
                    })->values();

                    $jumlahSiswaPerKelas = \App\Models\Siswa::selectRaw('kelas, COUNT(*) as total')
                        ->groupBy('kelas')
                        ->pluck('total', 'kelas');

                    $idJadwalHariIniMilikSaya = collect($jadwalHariIni)->pluck('id')->filter()->all();

                    $absenGuruHariIni = class_exists(\App\Models\AbsenGuru::class)
                        ? \App\Models\AbsenGuru::where('user_id', $userIdLogin)
                            ->whereDate('tanggal', date('Y-m-d'))
                            ->first()
                        : null;

                    $totalKelasHariIni   = count($jadwalHariIni);
                    $kelasSelesaiHariIni = collect($jadwalHariIni)->where('status', 'sudah')->count();
                    $persenProgress      = $totalKelasHariIni > 0 ? round(($kelasSelesaiHariIni / $totalKelasHariIni) * 100) : 0;

                    $totalSesiSaya = $semuaJadwalAdmin->filter(function ($item) use ($userIdLogin, $guruIdLogin, $namaGuruLogin, $idJadwalHariIniMilikSaya) {
                        return ($userIdLogin !== null && isset($item->user_id) && (int) $item->user_id === (int) $userIdLogin)
                            || ($guruIdLogin !== null && isset($item->guru_id) && (int) $item->guru_id === (int) $guruIdLogin)
                            || ($userIdLogin !== null && isset($item->guru_id) && (int) $item->guru_id === (int) $userIdLogin)
                            || ($namaGuruLogin !== '' && strcasecmp(trim((string) ($item->guru ?? $item->nama_guru ?? '')), $namaGuruLogin) === 0)
                            || in_array($item->id, $idJadwalHariIniMilikSaya);
                    })->count();
                @endphp


                <!-- CONTENT + CALENDAR -->
                <div class="flex flex-col lg:flex-row gap-6 items-start">

                    <!-- LEFT COLUMN -->
                    <div class="flex-1 space-y-6 min-w-0 w-full">

                        <!-- ================================================= -->
                        <!-- 4 KARTU STATISTIK RINGKAS (KPI MINI)              -->
                        <!-- ================================================= -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

                            <!-- KARTU 1: STATUS PRESENSI GURU -->
                            <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-slate-200 flex items-center space-x-3 transition hover:shadow-md">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full {{ $absenGuruHariIni ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center flex-shrink-0 text-sm">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Presensi Anda</p>
                                    @if($absenGuruHariIni)
                                        <p class="text-[11px] font-extrabold text-emerald-600 truncate">Sudah Absen</p>
                                    @else
                                        <p class="text-[11px] font-extrabold text-amber-600 truncate">Belum Absen</p>
                                    @endif
                                    <a href="{{ Route::has('guru.absen') ? route('guru.absen') : '#' }}" class="text-[9px] text-[#19A389] hover:underline font-bold">Presensi Saya <i class="fa-solid fa-arrow-right text-[8px]"></i></a>
                                </div>
                            </div>

                            <!-- KARTU 2: PROGRESS ABSENSI KELAS -->
                            <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between transition hover:shadow-md">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 text-sm">
                                        <i class="fa-solid fa-clipboard-user"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Absen Siswa</p>
                                        <p class="text-xs font-extrabold text-slate-700">{{ $kelasSelesaiHariIni }} / {{ $totalKelasHariIni }} Kelas</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div id="barAbsenSiswa" data-width="{{ $persenProgress }}" class="bg-[#19A389] h-full rounded-full transition-all duration-500 w-0"></div>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px] font-bold text-slate-400 mt-1">
                                        <span>Progres</span>
                                        <span>{{ $persenProgress }}%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- KARTU 3: TOTAL SESI MENGAJAR -->
                            <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-slate-200 flex items-center space-x-3 transition hover:shadow-md">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-sm">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Jadwal Minggu Ini</p>
                                    <p class="text-xs font-extrabold text-slate-700 truncate">{{ $totalSesiSaya }} Sesi Mengajar</p>
                                    <p class="text-[9px] text-indigo-500 font-bold">Senin s/d Sabtu</p>
                                </div>
                            </div>

                            <!-- KARTU 4: PENGINGAT AKTIF -->
                            <div class="bg-white p-3.5 rounded-2xl shadow-sm border border-slate-200 flex items-center space-x-3 transition hover:shadow-md">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 text-sm">
                                    <i class="fa-solid fa-bell"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pengingat Anda</p>
                                    <p id="counterPengingatKpi" class="text-xs font-extrabold text-slate-700">3 Catatan</p>
                                    <button onclick="openModal()" class="text-[9px] text-rose-500 hover:underline font-bold">Tambah Catatan <i class="fa-solid fa-plus text-[8px]"></i></button>
                                </div>
                            </div>

                        </div>

                        <!-- ================================================= -->
                        <!-- ABSENSI UTAMA (JAM KE-1) - sekali per kelas/hari  -->
                        <!-- ================================================= -->
                        @if($utamaHariIni->isNotEmpty())
                            <div>
                                <h3 class="font-bold text-slate-700 text-sm mb-1">Absensi Utama (Jam Ke-1)</h3>
                                <p class="text-[11px] text-slate-500 mb-3">Dilakukan satu kali oleh guru jam pertama. Terpisah dari absensi mata pelajaran.</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($utamaHariIni as $u)
                                        <div class="rounded-2xl p-4 shadow-md text-white flex flex-col justify-between min-h-[8.5rem]
                                                    bg-gradient-to-b {{ $u->status === 'mulai' ? 'from-indigo-500 to-indigo-800' : ($u->status === 'sudah' ? 'from-[#2f9e6e] to-[#1d6b4a]' : 'from-slate-400 to-slate-500') }}">
                                            <div>
                                                <div class="flex justify-between items-start gap-2">
                                                    <h4 class="font-bold text-sm">Kelas {{ $u->kelas }}</h4>
                                                    <span class="text-[10px] font-semibold bg-white/20 px-2 py-0.5 rounded-lg">Jam ke-1 • {{ $u->jam_masuk }}</span>
                                                </div>
                                                <p class="text-[11px] text-white/90 mt-1"><i class="fa-solid fa-users text-[9px] mr-1"></i>{{ $u->jumlah_siswa }} siswa</p>
                                                @if($u->status === 'sudah')
                                                    <p class="mt-1 text-[10px] font-semibold text-white/95">
                                                        <i class="fa-solid fa-lock mr-1"></i>Diselesaikan oleh: {{ $u->selesai_oleh }}
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                @if($u->status === 'mulai')
                                                    <a href="{{ route('guru.absen-utama.form', $u->jadwal_id) }}"
                                                       class="block text-center text-xs font-bold py-2 rounded-full bg-white text-indigo-800 hover:bg-indigo-50 transition">
                                                        Mulai Absensi Utama <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                                                    </a>
                                                @elseif($u->status === 'sudah')
                                                    <a href="{{ route('guru.absen-utama.form', $u->jadwal_id) }}"
                                                       class="block text-center text-xs font-bold py-2 rounded-full bg-white/25 hover:bg-white/35 transition">
                                                        <i class="fa-solid fa-eye mr-1"></i> Lihat (Terkunci)
                                                    </a>
                                                @else
                                                    <span class="block text-center text-xs font-bold py-2 rounded-full bg-white/25 cursor-not-allowed">
                                                        {{ $u->status === 'libur' ? 'Libur' : 'Belum Waktunya' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- ================================================= -->
                        <!-- JADWAL MENGAJAR HARI INI (kartu per jadwal)       -->
                        <!-- ================================================= -->

                        <div>

                            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-1 mb-3">
                                <h3 class="font-bold text-slate-700 text-sm">
                                    Jadwal Mengajar Hari Ini
                                </h3>
                                <p class="text-[11px] font-semibold text-slate-500">
                                    <i class="fa-regular fa-calendar mr-1 text-[#19A389]"></i>
                                    {{ \App\Services\AbsenSiswaService::tanggalPanjang($now) }}
                                </p>
                            </div>

                            @if($libur)
                                <div class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                    <span>Hari ini <b>libur</b>: {{ $libur->keterangan }}. Absensi siswa tidak dilakukan.</span>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

                                @forelse($jadwalHariIni as $k)

                                    @php
                                        $aktif = $k->status === 'mulai';
                                        $warna = match($k->status) {
                                            'mulai'      => 'from-[#19A389] to-[#0D6353]',
                                            'sudah'      => 'from-[#2f9e6e] to-[#1d6b4a]',
                                            default      => 'from-slate-400 to-slate-500',
                                        };
                                        $label = match($k->status) {
                                            'mulai'      => 'Mulai Absen',
                                            'sudah'      => 'Sudah Absen',
                                            'libur'      => 'Libur',
                                            'digantikan' => 'Digantikan',
                                            default      => 'Belum Waktunya',
                                        };
                                    @endphp

                                    <div class="bg-gradient-to-b {{ $warna }} rounded-2xl p-4 text-white shadow-md
                                                flex flex-col justify-between min-h-[10.5rem]">

                                        <div>
                                            <div class="flex justify-between items-start gap-2">
                                                <h4 class="font-bold text-sm">Kelas {{ $k->kelas }}</h4>
                                                <span class="text-[10px] font-semibold bg-white/20 px-2 py-0.5 rounded-lg whitespace-nowrap">
                                                    <i class="fa-regular fa-clock mr-0.5"></i>{{ $k->jam_masuk }} - {{ $k->jam_selesai }}
                                                </span>
                                            </div>

                                            <p class="text-[11px] text-white/90 mt-1">
                                                Mapel: <b>{{ $k->mapel }}</b>
                                            </p>

                                            @if($k->is_pengganti)
                                                <p class="mt-1 inline-block text-[10px] font-bold bg-amber-400 text-amber-950 px-2 py-0.5 rounded-md">
                                                    Guru Pengganti{{ $k->guru_asli ? ' (menggantikan ' . $k->guru_asli . ')' : '' }}
                                                </p>
                                            @endif

                                            @if($k->diganti_oleh)
                                                <p class="mt-1 text-[10px] font-semibold text-white/90">
                                                    Hari ini diajar oleh {{ $k->diganti_oleh }}
                                                </p>
                                            @endif

                                            @if($k->diabsen_oleh)
                                                <p class="mt-1 text-[10px] font-semibold text-white/95">
                                                    <i class="fa-solid fa-user-check mr-1"></i>Diabsen oleh: {{ $k->diabsen_oleh }}
                                                </p>
                                            @endif

                                        </div>

                                        <div class="mt-3">
                                            <p class="text-[10px] text-white/80 mb-2">
                                                <i class="fa-solid fa-users text-[9px] mr-1"></i>{{ $k->jumlah_siswa }} siswa terdaftar
                                            </p>

                                            @if($aktif)
                                                <a href="{{ route('guru.absen-siswa.form', $k->id) }}"
                                                   class="block text-center text-xs font-bold py-2 rounded-full
                                                          bg-emerald-200 text-emerald-900 hover:bg-emerald-100 transition">
                                                    {{ $label }} <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                                                </a>
                                            @else
                                                <span class="block text-center text-xs font-bold py-2 rounded-full
                                                             bg-white/25 text-white cursor-not-allowed">
                                                    @if($k->status === 'sudah')<i class="fa-solid fa-circle-check mr-1"></i>@endif
                                                    {{ $label }}
                                                </span>
                                            @endif

                                            @if($k->status === 'sudah' && ! $k->diganti_oleh)
                                                <a href="{{ route('guru.kelola-absensi.form', $k->id) }}"
                                                   class="mt-2 block text-center text-[11px] font-bold py-1.5 rounded-full bg-white/20 hover:bg-white/30 text-white transition">
                                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Kelola Absensi
                                                </a>
                                            @endif
                                        </div>

                                    </div>

                                @empty

                                    <div class="col-span-full p-6 bg-white rounded-2xl text-center
                                                text-slate-400 text-xs italic border border-slate-200">
                                        Tidak ada jadwal mengajar untuk Anda hari ini.
                                    </div>

                                @endforelse

                            </div>

                            <p class="text-[10px] text-slate-400 mt-3">
                                Absen siswa hanya dapat dimulai dari kartu ini. Tanggal otomatis hari ini dan tidak dapat diubah.
                                Salah input? Hapus absen hari tersebut di menu Rekap Absen Siswa, lalu absen ulang.
                            </p>

                        </div>


                        <!-- ================================================= -->
                        <!-- RINGKASAN SESI PEMBELAJARAN HARI INI (SESUAI SEMULA) -->
                        <!-- ================================================= -->

                        <div>

                            <h3 class="font-bold text-slate-700 text-sm mb-3">
                                Ringkasan Sesi Pembelajaran Hari Ini
                            </h3>

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-x-auto">

                                <table class="w-full text-left text-xs min-w-[500px]">

                                    <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                        <tr>
                                            <th class="p-3">KELAS</th>
                                            <th class="p-3">MAPEL</th>
                                            <th class="p-3">JAM</th>
                                            <th class="p-3">SISWA</th>
                                            <th class="p-3">STATUS</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100 text-slate-600">

                                        @forelse($jadwalHariIni as $k)
                                            <tr class="hover:bg-slate-50">
                                                <td class="p-3 font-semibold text-slate-700">Kelas {{ $k->kelas }}</td>
                                                <td class="p-3">{{ $k->mapel }}</td>
                                                <td class="p-3 font-bold text-slate-700">{{ $k->jam_masuk }} - {{ $k->jam_selesai }}</td>
                                                <td class="p-3">{{ $k->jumlah_siswa }}</td>
                                                <td class="p-3">
                                                    @php
                                                        $badge = match($k->status) {
                                                            'sudah'      => ['bg-emerald-100 text-emerald-800', 'Sudah Absen'],
                                                            'mulai'      => ['bg-teal-100 text-teal-800', 'Belum Diabsen'],
                                                            'libur'      => ['bg-rose-100 text-rose-700', 'Libur'],
                                                            'digantikan' => ['bg-amber-100 text-amber-800', 'Digantikan'],
                                                            default      => ['bg-slate-100 text-slate-600', 'Belum Waktunya'],
                                                        };
                                                    @endphp
                                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $badge[0] }}">{{ $badge[1] }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-4 text-center text-slate-400 italic">
                                                    Tidak ada jadwal aktif hari ini.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- SELURUH JADWAL MENGAJAR GURU (SESUAI DATA ADMIN)  -->
                        <!-- ================================================= -->

                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-700 text-sm">
                                        Jadwal Mengajar Guru (Seluruh Kelas)
                                    </h3>
                                    <p class="text-[11px] text-slate-500">
                                        Daftar seluruh jadwal pelajaran per hari dan kelas sesuai data jadwal mengajar di Admin
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <label class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm cursor-pointer select-none">
                                        <input type="checkbox" id="filterJadwalSaya" onchange="filterSeluruhJadwal()" class="rounded text-[#19A389] focus:ring-[#19A389]">
                                        <span>Hanya Jadwal Saya</span>
                                    </label>
                                    <span class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-[11px] font-bold text-slate-600 shadow-sm">
                                        Total: {{ $semuaJadwalAdmin->count() }} Jadwal
                                    </span>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                                <!-- Filter Hari & Pencarian -->
                                <div class="p-3 sm:px-4 bg-slate-50/80 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-2.5">
                                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                                        <button type="button"
                                                data-hari-admin="ALL"
                                                onclick="pilihTabHariAdmin('ALL')"
                                                class="tab-admin-hari px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition bg-[#19A389] text-white shadow-sm">
                                            Semua Hari ({{ $semuaJadwalAdmin->count() }})
                                        </button>

                                        @foreach($daftarHari as $hari)
                                            @php
                                                $cntHari = $semuaJadwalAdmin->filter(fn ($j) => strcasecmp(trim((string) ($j->hari ?? '')), $hari) === 0)->count();
                                                $isToday = strcasecmp($hari, $hariIniNama) === 0;
                                            @endphp
                                            <button type="button"
                                                    data-hari-admin="{{ $hari }}"
                                                    onclick="pilihTabHariAdmin('{{ $hari }}')"
                                                    class="tab-admin-hari px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 bg-white text-slate-600 hover:bg-emerald-50 border border-slate-200">
                                                @if($isToday)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                @endif
                                                <span>{{ $hari }}</span>
                                                <span class="text-[10px] px-1.5 rounded bg-slate-100 text-slate-600">{{ $cntHari }}</span>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="relative w-full md:w-56">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                                        <input type="text"
                                               id="cariJadwalAdmin"
                                               oninput="filterSeluruhJadwal()"
                                               placeholder="Cari kelas / mapel / guru..."
                                               class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                                    </div>
                                </div>

                                <!-- Tabel Seluruh Jadwal -->
                                <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                                    <table class="w-full text-left text-xs min-w-[650px]">
                                        <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold text-[11px] sticky top-0 z-10">
                                            <tr>
                                                <th class="p-3">HARI</th>
                                                <th class="p-3">KELAS</th>
                                                <th class="p-3">MAPEL</th>
                                                <th class="p-3">GURU PENGAJAR</th>
                                                <th class="p-3">JAM</th>
                                                <th class="p-3 text-center">SISWA</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600">
                                            @forelse($semuaJadwalAdmin as $item)
                                                @php
                                                    $hariStr   = ucfirst(strtolower(trim((string) ($item->hari ?? '-'))));
                                                    $isHariIni = strcasecmp($hariStr, $hariIniNama) === 0;

                                                    $namaGuruItem = $item->guru
                                                        ?? $item->nama_guru
                                                        ?? (isset($item->guru_id) ? ($mapNamaGuru[$item->guru_id] ?? $mapNamaUser[$item->guru_id] ?? null) : null)
                                                        ?? (isset($item->user_id) ? ($mapNamaUser[$item->user_id] ?? null) : null)
                                                        ?? '-';

                                                    if (is_object($namaGuruItem)) {
                                                        $namaGuruItem = $namaGuruItem->nama ?? $namaGuruItem->name ?? '-';
                                                    }

                                                    $mapelItem = $item->mapel ?? $item->mata_pelajaran ?? '-';

                                                    $isMilikSaya = ($userIdLogin !== null && isset($item->user_id) && (int) $item->user_id === (int) $userIdLogin)
                                                        || ($guruIdLogin !== null && isset($item->guru_id) && (int) $item->guru_id === (int) $guruIdLogin)
                                                        || ($userIdLogin !== null && isset($item->guru_id) && (int) $item->guru_id === (int) $userIdLogin)
                                                        || ($namaGuruLogin !== '' && strcasecmp(trim((string) $namaGuruItem), $namaGuruLogin) === 0)
                                                        || in_array($item->id, $idJadwalHariIniMilikSaya);

                                                    $jmlSiswaItem = $jumlahSiswaPerKelas[$item->kelas] ?? 0;
                                                @endphp
                                                <tr data-hari="{{ $hariStr }}"
                                                    data-milik-saya="{{ $isMilikSaya ? '1' : '0' }}"
                                                    data-search="{{ strtolower($hariStr . ' ' . $item->kelas . ' ' . $mapelItem . ' ' . $namaGuruItem) }}"
                                                    class="row-jadwal-admin transition {{ $isHariIni ? 'bg-emerald-50/40 hover:bg-emerald-50/80' : 'hover:bg-slate-50' }}">
                                                    <td class="p-3 font-bold whitespace-nowrap">
                                                        <span class="{{ $isHariIni ? 'text-[#0D6353]' : 'text-slate-700' }}">{{ $hariStr }}</span>
                                                        @if($isHariIni)
                                                            <span class="ml-1 px-1.5 py-0.5 rounded-full bg-[#19A389] text-white text-[9px] font-extrabold">Hari Ini</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3 font-bold text-slate-800 whitespace-nowrap">Kelas {{ $item->kelas }}</td>
                                                    <td class="p-3 font-medium text-slate-700">{{ $mapelItem }}</td>
                                                    <td class="p-3">
                                                        <span class="font-semibold {{ $isMilikSaya ? 'text-[#0D6353]' : 'text-slate-700' }}">
                                                            {{ $namaGuruItem }}
                                                        </span>
                                                        @if($isMilikSaya)
                                                            <span class="ml-1 px-1.5 py-0.5 rounded bg-emerald-100 text-[#0D6353] text-[9px] font-bold">Anda</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-3 font-bold text-slate-700 whitespace-nowrap">
                                                        {{ substr((string) $item->jam_masuk, 0, 5) }} - {{ substr((string) $item->jam_selesai, 0, 5) }}
                                                    </td>
                                                    <td class="p-3 text-center font-semibold">{{ $jmlSiswaItem }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="p-6 text-center text-slate-400 italic">
                                                        Belum ada data jadwal mengajar yang diinput Admin.
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr id="emptyJadwalAdminRow" class="hidden">
                                                <td colspan="6" class="p-6 text-center text-slate-400 italic">
                                                    Tidak ada jadwal yang sesuai dengan filter yang dipilih.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- RIGHT COLUMN (Kalender, Pengingat, & Akses Cepat) -->
                    <!-- ================================================= -->
                    <div id="calendarPanel"
                         class="hidden lg:block w-full lg:w-80 space-y-6 flex-shrink-0 animate-in fade-in duration-300">

                        <!-- CALENDAR -->
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">

                            <div class="flex justify-between items-center mb-3">

                                <div class="flex items-center space-x-1">

                                    <button onclick="changeYear(-1)"
                                            title="Tahun Lalu"
                                            class="text-slate-400 hover:text-slate-700 text-xs px-1 font-bold">

                                        <i class="fa-solid fa-angles-left"></i>

                                    </button>

                                    <button onclick="changeMonth(-1)"
                                            title="Bulan Lalu"
                                            class="text-slate-400 hover:text-slate-700 text-xs px-1">

                                        <i class="fa-solid fa-chevron-left"></i>

                                    </button>

                                </div>

                                <h4 id="calendarHeaderTitle"
                                    class="font-extrabold text-xs text-slate-700 cursor-pointer select-none"
                                    onclick="resetToToday()">

                                    Januari 2026

                                </h4>

                                <div class="flex items-center space-x-1">

                                    <button onclick="changeMonth(1)"
                                            title="Bulan Depan"
                                            class="text-slate-400 hover:text-slate-700 text-xs px-1">

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </button>

                                    <button onclick="changeYear(1)"
                                            title="Tahun Depan"
                                            class="text-slate-400 hover:text-slate-700 text-xs px-1 font-bold">

                                        <i class="fa-solid fa-angles-right"></i>

                                    </button>

                                </div>

                            </div>

                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-semibold text-slate-400 mb-2">

                                <div>Mon</div>
                                <div>Tue</div>
                                <div>Wed</div>
                                <div>Thu</div>
                                <div>Fri</div>
                                <div>Sat</div>
                                <div>Sun</div>

                            </div>

                            <div id="calendarDaysGrid"
                                 class="grid grid-cols-7 gap-1 text-center text-xs text-slate-600">
                            </div>

                            <div class="mt-4 flex justify-center">

                                <button onclick="openModal()"
                                        class="w-full py-2 bg-[#19A389] hover:bg-[#138A73] text-white font-extrabold rounded-xl text-xs shadow transition">

                                    INPUT CATATAN BARU

                                </button>

                            </div>

                        </div>


                        <!-- REMINDER & AKSES CEPAT -->
                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">

                            <h4 class="font-bold text-xs text-slate-700 mb-3 flex items-center justify-between">

                                <span>Pengingat Catatan Tugas</span>

                                <i class="fa-regular fa-calendar-check text-slate-400"></i>

                            </h4>

                            <div id="reminderList" class="space-y-3 mb-5"></div>

                            <!-- AKSES CEPAT & JAM DIGITAL REAL-TIME -->
                            <div class="border-t border-slate-100 pt-4">

                                <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center justify-between">
                                    <span>Akses Cepat</span>
                                    <i class="fa-solid fa-bolt text-amber-500"></i>
                                </h5>

                                <div class="grid grid-cols-2 gap-2 text-[10px] font-bold">
                                    <a href="{{ Route::has('guru.absen') ? route('guru.absen') : '#' }}"
                                       class="p-2 border border-slate-200 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 text-center hover:border-emerald-200 transition">
                                        <i class="fa-regular fa-clock mr-1 text-[#19A389]"></i>Presensi Saya
                                    </a>
                                    <a href="{{ Route::has('guru.izin.index') ? route('guru.izin.index') : '#' }}"
                                       class="p-2 border border-slate-200 rounded-xl bg-slate-50 hover:bg-emerald-50 text-slate-600 text-center hover:border-emerald-200 transition">
                                        <i class="fa-solid fa-user-gear mr-1 text-[#19A389]"></i>Ajukan Izin
                                    </a>
                                    <a href="{{ Route::has('guru.rekap-siswa') ? route('guru.rekap-siswa') : '#' }}"
                                       class="col-span-2 p-2 border border-slate-200 rounded-xl bg-slate-50 hover:bg-[#19A389] hover:text-white text-slate-600 text-center hover:border-[#19A389] transition">
                                        <i class="fa-solid fa-users-viewfinder mr-1"></i>Rekap Absen Siswa
                                    </a>
                                </div>

                                <!-- JAM DIGITAL REAL-TIME -->
                                <div class="mt-4 bg-[#EAF0F6] rounded-xl p-3 text-center border border-slate-200">
                                    <div id="digitalClock" class="text-base font-extrabold text-[#0D6353]">00:00:00 WIB</div>
                                    <div class="text-[9px] text-[#19A389] font-bold mt-0.5">WAKTU SEKARANG</div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </main>

    </div>


    <!-- ============================================================= -->
    <!-- MODAL PENGINGAT -->
    <!-- ============================================================= -->

    <div id="noteModal"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50
                flex items-center justify-center hidden p-4">

        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl
                    overflow-hidden border border-slate-100
                    animate-in fade-in zoom-in duration-200">

            <div class="bg-white p-5 border-b border-slate-100
                        flex justify-between items-center">

                <h3 class="font-bold text-slate-800 text-sm sm:text-base
                           tracking-wide uppercase">

                    PENGINGAT CATATAN TUGAS

                </h3>

                <button onclick="closeModal()"
                        class="text-slate-400 hover:text-slate-600 p-1">

                    <i class="fa-solid fa-xmark text-lg"></i>

                </button>

            </div>


            <form id="noteForm"
                  onsubmit="saveNote(event)"
                  class="p-5 sm:p-6 space-y-4">

                <div>

                    <label class="block text-xs font-semibold
                                  text-slate-700 mb-1">

                        Pembelajaran

                    </label>

                    <input type="text"
                           id="pembelajaran"
                           placeholder="Eng - Vocabulary"
                           required
                           class="w-full text-xs px-3 py-2.5
                                  bg-slate-50 border border-slate-200
                                  rounded-lg focus:outline-none
                                  focus:border-[#19A389]
                                  focus:bg-white transition">

                </div>


                <div>

                    <label class="block text-xs font-semibold
                                  text-slate-700 mb-1">

                        Materi Pembelajaran

                    </label>

                    <input type="text"
                           id="materi"
                           placeholder="Materi Bab 5"
                           required
                           class="w-full text-xs px-3 py-2.5
                                  bg-slate-50 border border-slate-200
                                  rounded-lg focus:outline-none
                                  focus:border-[#19A389]
                                  focus:bg-white transition">

                </div>


                <div>

                    <label class="block text-xs font-semibold
                                  text-slate-700 mb-1">

                        Tanggal

                    </label>

                    <div class="relative flex items-center">

                        <input type="text"
                               id="tanggal"
                               placeholder="12-Jan-2026 atau ddmmyy (misal: 120126)"
                               required
                               onblur="formatDateInput(this)"
                               class="w-full text-xs px-3 py-2.5
                                      bg-slate-50 border border-slate-200
                                      rounded-lg focus:outline-none
                                      focus:border-[#19A389]
                                      focus:bg-white transition pr-10">

                        <input type="date"
                               id="nativeDatePicker"
                               onchange="syncNativeDate(this)"
                               class="absolute right-2 opacity-0
                                      w-6 h-6 cursor-pointer">

                        <button type="button"
                                onclick="document.getElementById('nativeDatePicker').showPicker()"
                                class="absolute right-3 text-slate-400
                                       hover:text-[#19A389] text-xs">

                            <i class="fa-regular fa-calendar-days"></i>

                        </button>

                    </div>

                </div>


                <div class="pt-4 flex justify-center">

                    <button type="submit"
                            class="w-full max-w-xs py-2.5
                                   bg-[#19A389] hover:bg-[#138A73]
                                   text-white font-semibold rounded-xl
                                   text-xs shadow transition">

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- ============================================================= -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================= -->

    <script>

        let currentDate = new Date();
        let hariAdminTerpilih = 'ALL';

        const monthNames = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        const monthNamesShort = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
            "Jul",
            "Agust",
            "Sep",
            "Okt",
            "Nov",
            "Des"
        ];


        const initialReminders = [
            {
                id: 1,
                title: "Eng - Vocabulary test",
                date: "12-jan-2026"
            },
            {
                id: 2,
                title: "Eng - Speaking Class",
                date: "12 Jan 2026, Senin"
            },
            {
                id: 3,
                title: "Pemeriksaan Tugas Mandiri",
                date: "12 Jan 2026, Senin"
            }
        ];


        document.addEventListener("DOMContentLoaded", function () {

            // Set lebar progress bar absensi siswa secara dinamis tanpa inline style di Blade
            const barAbsen = document.getElementById('barAbsenSiswa');
            if (barAbsen) {
                barAbsen.style.width = (barAbsen.dataset.width || 0) + '%';
            }

            renderCalendar();
            loadReminders();
            updateDigitalClock();
            setInterval(updateDigitalClock, 1000);

        });

        // ===== UPDATE JAM DIGITAL REAL-TIME =====
        function updateDigitalClock() {
            const el = document.getElementById('digitalClock');
            if (!el) return;
            const t = new Date();
            const h = String(t.getHours()).padStart(2, '0');
            const m = String(t.getMinutes()).padStart(2, '0');
            const s = String(t.getSeconds()).padStart(2, '0');
            el.innerText = `${h}:${m}:${s} WIB`;
        }

        // ===== FILTER SELURUH JADWAL MENGAJAR GURU (DATA ADMIN) =====
        function pilihTabHariAdmin(hari) {
            hariAdminTerpilih = hari;
            document.querySelectorAll('.tab-admin-hari').forEach(btn => {
                const aktif = btn.dataset.hariAdmin === hari;
                if (aktif) {
                    btn.className = 'tab-admin-hari px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 bg-[#19A389] text-white shadow-sm';
                } else {
                    btn.className = 'tab-admin-hari px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 bg-white text-slate-600 hover:bg-emerald-50 border border-slate-200';
                }
            });
            filterSeluruhJadwal();
        }

        function filterSeluruhJadwal() {
            const hanyaSaya = document.getElementById('filterJadwalSaya')?.checked || false;
            const keyword   = (document.getElementById('cariJadwalAdmin')?.value || '').trim().toLowerCase();
            const rows      = document.querySelectorAll('.row-jadwal-admin');
            const emptyRow  = document.getElementById('emptyJadwalAdminRow');

            let visibleCount = 0;
            rows.forEach(row => {
                const matchHari   = hariAdminTerpilih === 'ALL' || row.dataset.hari.toLowerCase() === hariAdminTerpilih.toLowerCase();
                const matchGuru   = !hanyaSaya || row.dataset.milikSaya === '1';
                const matchSearch = keyword === '' || row.dataset.search.includes(keyword);

                const show = matchHari && matchGuru && matchSearch;
                row.classList.toggle('hidden', !show);
                if (show) visibleCount++;
            });

            if (emptyRow && rows.length > 0) {
                emptyRow.classList.toggle('hidden', visibleCount > 0);
            }
        }


        // ===== TOAST NOTIFIKASI =====
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('toastData');
            if (!el) return;

            const sukses = el.dataset.tipe === 'success';
            const toast = document.createElement('div');
            toast.className = 'fixed top-5 right-5 z-[100] max-w-sm w-[calc(100%-2.5rem)] p-4 rounded-2xl shadow-xl border-l-4 '
                + 'bg-white flex items-start gap-3 transition-all duration-300 opacity-0 -translate-y-2 '
                + (sukses ? 'border-[#19A389]' : 'border-rose-500');

            const ikon = document.createElement('i');
            ikon.className = 'mt-0.5 text-lg fa-solid ' + (sukses ? 'fa-circle-check text-[#19A389]' : 'fa-circle-exclamation text-rose-500');

            const isi = document.createElement('div');
            isi.className = 'flex-1';
            const judul = document.createElement('p');
            judul.className = 'text-xs font-bold text-slate-800';
            judul.textContent = sukses ? 'Berhasil' : 'Tidak dapat diproses';
            const pesan = document.createElement('p');
            pesan.className = 'text-[11px] text-slate-500 mt-0.5 leading-relaxed';
            pesan.textContent = el.dataset.pesan;
            isi.append(judul, pesan);

            const tutup = document.createElement('button');
            tutup.type = 'button';
            tutup.className = 'text-slate-400 hover:text-slate-600 text-xs';
            tutup.innerHTML = '<i class="fa-solid fa-xmark"></i>';

            const hilang = () => { toast.classList.add('opacity-0'); setTimeout(() => toast.remove(), 300); };
            tutup.addEventListener('click', hilang);

            toast.append(ikon, isi, tutup);
            document.body.appendChild(toast);
            requestAnimationFrame(() => toast.classList.remove('opacity-0', '-translate-y-2'));
            setTimeout(hilang, 7000);
        });

        function toggleSidebar() {

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }

            if (overlay) {
                overlay.classList.toggle('hidden');
            }

        }


        function toggleCalendarPanel() {

            const panel = document.getElementById('calendarPanel');
            if (!panel) return;

            const isDesktop = window.innerWidth >= 1024;

            if (isDesktop) {
                if (panel.classList.contains('lg:hidden')) {
                    panel.classList.remove('lg:hidden');
                    panel.classList.add('lg:block');
                } else {
                    panel.classList.remove('lg:block');
                    panel.classList.add('lg:hidden');
                }
            } else {
                const akanDibuka = panel.classList.contains('hidden');
                panel.classList.toggle('hidden');
                if (akanDibuka) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

        }


        function getRemindersFromStorage() {

            const stored = localStorage.getItem('guru_reminders');

            if (stored) {

                return JSON.parse(stored);

            } else {

                localStorage.setItem(
                    'guru_reminders',
                    JSON.stringify(initialReminders)
                );

                return initialReminders;

            }

        }


        function loadReminders() {

            const reminders = getRemindersFromStorage();

            const reminderList =
                document.getElementById('reminderList');

            const countKpi = document.getElementById('counterPengingatKpi');

            if (countKpi) {
                countKpi.innerText = `${reminders.length} Catatan`;
            }

            if (!reminderList) {
                return;
            }

            reminderList.innerHTML = '';


            if (reminders.length === 0) {

                reminderList.innerHTML = `
                    <p class="text-xs text-slate-400
                              italic text-center py-2">
                        Tidak ada pengingat.
                    </p>
                `;

                return;

            }


            reminders.forEach(item => {

                const div = document.createElement('div');

                div.className =
                    'group flex items-start justify-between space-x-2 ' +
                    'text-xs bg-slate-50/50 hover:bg-slate-50 p-2 ' +
                    'rounded-lg border border-slate-100 transition';


                div.innerHTML = `

                    <div class="flex items-start space-x-3">

                        <div class="p-1.5 bg-slate-100
                                    text-slate-500 rounded
                                    flex-shrink-0 mt-0.5">

                            <i class="fa-regular fa-bell"></i>

                        </div>

                        <div>

                            <p class="font-semibold text-slate-700">
                                ${item.title}
                            </p>

                            <p class="text-[10px] text-slate-400">
                                ${item.date}
                            </p>

                        </div>

                    </div>

                    <button onclick="deleteNote(${item.id})"
                            title="Hapus Catatan"
                            class="text-slate-300
                                   hover:text-rose-500 p-1
                                   transition opacity-80
                                   group-hover:opacity-100">

                        <i class="fa-regular fa-trash-can text-xs"></i>

                    </button>

                `;

                reminderList.appendChild(div);

            });

        }


        function saveNote(event) {

            event.preventDefault();

            const pembelajaran =
                document.getElementById('pembelajaran').value;

            const tanggalInput =
                document.getElementById('tanggal').value;


            if (!pembelajaran || !tanggalInput) {
                return;
            }


            const reminders = getRemindersFromStorage();


            const newReminder = {

                id: Date.now(),

                title: pembelajaran,

                date: tanggalInput

            };


            reminders.unshift(newReminder);


            localStorage.setItem(
                'guru_reminders',
                JSON.stringify(reminders)
            );


            loadReminders();

            document.getElementById('noteForm').reset();

            closeModal();

        }


        function deleteNote(id) {

            let reminders = getRemindersFromStorage();

            reminders =
                reminders.filter(item => item.id !== id);

            localStorage.setItem(
                'guru_reminders',
                JSON.stringify(reminders)
            );

            loadReminders();

        }


        function openModal() {

            document.getElementById('noteModal')
                .classList.remove('hidden');

        }


        function closeModal() {

            document.getElementById('noteModal')
                .classList.add('hidden');

        }


        function formatDateInput(input) {

            let val = input.value.trim();

            if (/^\d{6}$/.test(val)) {

                let day = val.substring(0, 2);

                let monthIdx =
                    parseInt(val.substring(2, 4)) - 1;

                let year =
                    "20" + val.substring(4, 6);


                if (monthIdx >= 0 && monthIdx < 12) {

                    input.value =
                        `${day} ${monthNamesShort[monthIdx]} ${year}`;

                }

            }

        }


        function syncNativeDate(picker) {

            if (picker.value) {

                const parts =
                    picker.value.split('-');

                const year = parts[0];

                const monthIdx =
                    parseInt(parts[1]) - 1;

                const day = parts[2];


                document.getElementById('tanggal').value =
                    `${day} ${monthNamesShort[monthIdx]} ${year}`;

            }

        }


        function renderCalendar() {

            const year =
                currentDate.getFullYear();

            const month =
                currentDate.getMonth();


            const headerEl =
                document.getElementById('calendarHeaderTitle');


            if (headerEl) {

                headerEl.innerText =
                    `${monthNames[month]} ${year}`;

            }


            const daysGrid =
                document.getElementById('calendarDaysGrid');


            if (!daysGrid) {
                return;
            }


            daysGrid.innerHTML = '';


            const firstDayIndex =
                new Date(year, month, 1).getDay();

            const adjustedFirstDay =
                (firstDayIndex === 0)
                    ? 6
                    : firstDayIndex - 1;


            const totalDays =
                new Date(year, month + 1, 0).getDate();


            const prevMonthTotalDays =
                new Date(year, month, 0).getDate();


            for (
                let i = adjustedFirstDay;
                i > 0;
                i--
            ) {

                const dayDiv =
                    document.createElement('div');

                dayDiv.className =
                    'py-1.5 text-slate-300';

                dayDiv.innerText =
                    prevMonthTotalDays - i + 1;

                daysGrid.appendChild(dayDiv);

            }


            const today = new Date();


            for (
                let day = 1;
                day <= totalDays;
                day++
            ) {

                const dayDiv =
                    document.createElement('div');

                dayDiv.className =
                    'py-1.5 rounded-lg hover:bg-slate-100 ' +
                    'cursor-pointer transition';


                if (
                    day === today.getDate() &&
                    month === today.getMonth() &&
                    year === today.getFullYear()
                ) {

                    dayDiv.className =
                        'py-1.5 rounded-lg bg-[#19A389] ' +
                        'text-white font-bold shadow-sm';

                }


                dayDiv.innerText = day;

                daysGrid.appendChild(dayDiv);

            }

        }


        function changeMonth(step) {

            currentDate.setMonth(
                currentDate.getMonth() + step
            );

            renderCalendar();

        }


        function changeYear(step) {

            currentDate.setFullYear(
                currentDate.getFullYear() + step
            );

            renderCalendar();

        }


        function resetToToday() {

            currentDate = new Date();

            renderCalendar();

        }

    </script>

@include('layouts.partials.ui')
</body>
</html>