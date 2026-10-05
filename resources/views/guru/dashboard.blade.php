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
                                <a href="{{ Route::has('guru.absen-siswa') ? route('guru.absen-siswa') : '#' }}"
                                   class="hover:text-white flex items-center gap-2
                                          px-4 py-2 transition-all
                                          rounded-l-full rounded-r-none
                                          {{ request()->routeIs('guru.absen-siswa')
                                              ? 'bg-white text-[#138A73] font-bold shadow'
                                              : 'hover:bg-white/10 rounded-r-lg' }}">

                                    <span class="text-sm font-bold">•</span>
                                    Absen Siswa

                                </a>
                            </li>

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
                                onclick="return confirmLogout(event)"
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
                            title="Buka / Tutup Kalender Pengingat"
                            class="text-slate-500 hover:text-slate-800
                                   p-2 rounded-lg bg-white/50 hover:bg-white
                                   shadow-sm transition active:scale-95">

                        <i class="fa-solid fa-ellipsis-vertical text-xl"></i>

                    </button>

                </div>

            </header>


            <!-- BODY -->
            <div class="px-4 sm:px-8 pb-8 space-y-6">

                <!-- SUCCESS NOTIFICATION -->
                @if(session('success'))

                    <div class="p-4 bg-white/90 backdrop-blur-md
                                border-l-4 border-[#19A389] rounded-2xl
                                shadow-sm flex justify-between items-center
                                transition-all animate-in fade-in">

                        <div class="flex items-center gap-3">

                            <div class="w-9 h-9 rounded-xl bg-emerald-100
                                        text-[#19A389] flex items-center
                                        justify-center font-bold flex-shrink-0">

                                <i class="fa-solid fa-circle-check text-lg"></i>

                            </div>

                            <div>

                                <h4 class="text-xs font-bold text-slate-800">
                                    Sistem Presensi Berhasil
                                </h4>

                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ session('success') }}
                                </p>

                            </div>

                        </div>

                        <button onclick="this.parentElement.remove()"
                                class="text-slate-400 hover:text-slate-600
                                       text-xs px-2 py-1">

                            <i class="fa-solid fa-xmark"></i>

                        </button>

                    </div>

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


                <!-- CONTENT + CALENDAR -->
                <div class="flex flex-col lg:flex-row gap-6">

                    <!-- LEFT COLUMN -->
                    <div class="flex-1 space-y-6 min-w-0">

                        <!-- ================================================= -->
                        <!-- KELAS MENGAJAR -->
                        <!-- ================================================= -->

                        <div>

                            <h3 class="font-bold text-slate-700 text-sm mb-3">
                                Kelas Mengajar Anda
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2
                                        xl:grid-cols-3 gap-4">

                                @forelse($kelasesWithCount as $k)

                                    <a href="{{ route('guru.absen-siswa', [
                                            'kelas' => $k->nama_kelas,
                                            'mapel' => $mapelGuru
                                        ]) }}"
                                       class="bg-gradient-to-b from-[#19A389] to-[#0D6353]
                                              rounded-2xl p-4 text-white shadow-md
                                              flex flex-col justify-between h-36
                                              hover:scale-[1.02] transition-all
                                              cursor-pointer group">

                                        <div>

                                            <div class="flex justify-between items-start">

                                                <h4 class="font-bold text-sm
                                                           group-hover:underline">

                                                    Kelas {{ $k->nama_kelas }}

                                                </h4>

                                                <span class="text-[9px]
                                                             bg-white/20 px-2 py-0.5
                                                             rounded-lg font-semibold
                                                             flex items-center gap-1">

                                                    <span>Mulai Absen</span>

                                                    <i class="fa-solid fa-arrow-right
                                                              text-[10px]"></i>

                                                </span>

                                            </div>

                                            <p class="text-[11px] text-emerald-100 mt-1">

                                                Mapel:
                                                <b>{{ $mapelGuru }}</b>

                                            </p>

                                        </div>

                                        <div class="text-[11px] text-white/90 font-medium">

                                            <p>
                                                <i class="fa-solid fa-users
                                                          text-[9px] mr-1"></i>

                                                {{ $k->jumlah_murid }}
                                                Murid Terdaftar
                                            </p>

                                            <p>
                                                <i class="fa-regular fa-user
                                                          text-[9px] mr-1"></i>

                                                Wali kelas:
                                                {{ $guru->nama ?? Auth::user()->name }}
                                            </p>

                                        </div>

                                    </a>

                                @empty

                                    <div class="col-span-full p-6 bg-white
                                                rounded-2xl text-center
                                                text-slate-400 text-xs italic
                                                border border-slate-200">

                                        Belum ada data kelas mengajar
                                        yang diatur untuk akun Anda.

                                    </div>

                                @endforelse

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- JADWAL / SESI PEMBELAJARAN -->
                        <!-- ================================================= -->

                        <div>

                            <h3 class="font-bold text-slate-700 text-sm mb-3">
                                Jadwal / Sesi Pembelajaran Hari Ini
                            </h3>

                            <div class="bg-white rounded-2xl shadow-sm
                                        border border-slate-200 overflow-x-auto">

                                <table class="w-full text-left text-xs
                                              min-w-[500px]">

                                    <thead class="bg-gradient-to-r
                                                  from-[#19A389] to-[#138A73]
                                                  text-white uppercase font-bold">

                                        <tr>

                                            <th class="p-3">
                                                KELAS
                                            </th>

                                            <th class="p-3">
                                                WALI KELAS
                                            </th>

                                            <th class="p-3">
                                                JUMLAH MURID
                                            </th>

                                            <th class="p-3">
                                                JAM MASUK
                                            </th>

                                            <th class="p-3">
                                                JAM PULANG
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody class="divide-y divide-slate-100
                                                 text-slate-600">

                                        @forelse($kelasesWithCount as $k)

                                            <tr class="hover:bg-slate-50">

                                                <td class="p-3 font-semibold
                                                           text-slate-700">

                                                    Kelas {{ $k->nama_kelas }}

                                                </td>

                                                <td class="p-3">

                                                    {{ $guru->nama ?? Auth::user()->name }}

                                                </td>

                                                <td class="p-3">

                                                    <span class="px-2.5 py-1
                                                                 bg-emerald-100
                                                                 text-emerald-800
                                                                 rounded-full font-bold
                                                                 text-[10px]">

                                                        {{ $k->jumlah_murid }}
                                                        Murid

                                                    </span>

                                                </td>

                                                <td class="p-3 font-bold
                                                           text-slate-700">

                                                    {{ $k->jam_masuk }}

                                                </td>

                                                <td class="p-3 font-bold
                                                           text-slate-700">

                                                    {{ $k->jam_pulang }}

                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="5"
                                                    class="p-4 text-center
                                                           text-slate-400 italic">

                                                    Tidak ada jadwal aktif hari ini.

                                                </td>

                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- RIGHT COLUMN -->
                    <!-- ================================================= -->

                    <div id="calendarPanel"
                         class="hidden w-full lg:w-80 space-y-6
                                flex-shrink-0 animate-in fade-in duration-300">

                        <!-- CALENDAR -->
                        <div class="bg-white p-4 rounded-xl shadow-sm
                                    border border-slate-200">

                            <div class="flex justify-between items-center mb-3">

                                <div class="flex items-center space-x-1">

                                    <button onclick="changeYear(-1)"
                                            title="Tahun Lalu"
                                            class="text-slate-400 hover:text-slate-700
                                                   text-xs px-1 font-bold">

                                        <i class="fa-solid fa-angles-left"></i>

                                    </button>

                                    <button onclick="changeMonth(-1)"
                                            title="Bulan Lalu"
                                            class="text-slate-400 hover:text-slate-700
                                                   text-xs px-1">

                                        <i class="fa-solid fa-chevron-left"></i>

                                    </button>

                                </div>

                                <h4 id="calendarHeaderTitle"
                                    class="font-bold text-xs text-slate-700
                                           cursor-pointer select-none"
                                    onclick="resetToToday()">

                                    Januari 2026

                                </h4>

                                <div class="flex items-center space-x-1">

                                    <button onclick="changeMonth(1)"
                                            title="Bulan Depan"
                                            class="text-slate-400 hover:text-slate-700
                                                   text-xs px-1">

                                        <i class="fa-solid fa-chevron-right"></i>

                                    </button>

                                    <button onclick="changeYear(1)"
                                            title="Tahun Depan"
                                            class="text-slate-400 hover:text-slate-700
                                                   text-xs px-1 font-bold">

                                        <i class="fa-solid fa-angles-right"></i>

                                    </button>

                                </div>

                            </div>

                            <div class="grid grid-cols-7 gap-1 text-center
                                        text-[10px] font-semibold text-slate-400 mb-2">

                                <div>Mon</div>
                                <div>Tue</div>
                                <div>Wed</div>
                                <div>Thu</div>
                                <div>Fri</div>
                                <div>Sat</div>
                                <div>Sun</div>

                            </div>

                            <div id="calendarDaysGrid"
                                 class="grid grid-cols-7 gap-1
                                        text-center text-xs text-slate-600">
                            </div>

                            <div class="mt-4 flex justify-center">

                                <button onclick="openModal()"
                                        class="w-full py-2 bg-[#19A389]
                                               hover:bg-[#138A73] text-white
                                               font-semibold rounded-lg text-xs
                                               shadow transition">

                                    INPUT

                                </button>

                            </div>

                        </div>


                        <!-- REMINDER -->
                        <div class="bg-white p-4 rounded-xl shadow-sm
                                    border border-slate-200">

                            <h4 class="font-bold text-xs text-slate-700 mb-3
                                       flex items-center justify-between">

                                <span>Pengingat</span>

                                <i class="fa-regular fa-calendar-check
                                          text-slate-400"></i>

                            </h4>

                            <div id="reminderList" class="space-y-3"></div>

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
                title: "Tugas Pengayaan Bahasa",
                date: "12-jan-2026"
            },
            {
                id: 2,
                title: "Vocabulary test",
                date: "12 Jan 2026, Senin"
            },
            {
                id: 3,
                title: "Pemeriksaan Tugas Mandiri",
                date: "12 Jan 2026, Senin"
            }
        ];


        document.addEventListener("DOMContentLoaded", function () {

            renderCalendar();
            loadReminders();

        });


        function confirmLogout(event) {

            const confirmed = confirm(
                "Apakah Anda yakin ingin keluar dari akun ini?"
            );

            if (!confirmed) {

                event.preventDefault();

                return false;

            }

            return true;

        }


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

            if (panel) {
                panel.classList.toggle('hidden');
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

</body>
</html>