<aside id="sidebar" class="fixed lg:static top-0 left-0 z-50 w-64 bg-gradient-to-b from-[#19A389] via-[#138A73] to-[#0D6353] text-white flex flex-col justify-between py-6 px-0 flex-shrink-0 h-screen lg:h-auto min-h-screen transition-transform duration-300 -translate-x-full lg:translate-x-0 overflow-y-auto rounded-tr-3xl rounded-br-3xl shadow-xl">
    <div>
        <!-- Brand Header / Logo Center -->
        <div class="flex flex-col items-center text-center pt-2 pb-6 px-4">
            <div class="w-20 h-20 flex items-center justify-center mb-2">
                <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}" 
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=SMP+D&background=19A389&color=fff';" 
                     alt="Logo SMP Djojoredjo" class="w-full h-full object-contain rounded-full drop-shadow-md">
            </div>
            <span class="text-xs font-extrabold tracking-wider text-white">SMP DJOJOREDJO</span>
            <span class="text-[10px] text-emerald-200 uppercase font-semibold tracking-wide">
                {{ Auth::user()->role ?? 'SISTEM SEKOLAH' }}
            </span>
        </div>

        <!-- User Info Profile -->
        <div class="mx-4 flex items-center space-x-3 p-2.5 bg-white/10 border border-white/20 rounded-xl mb-6 shadow-inner backdrop-blur-sm">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="overflow-hidden">
                <h2 class="font-semibold text-xs text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</h2>
                <p class="text-[10px] text-emerald-200 uppercase font-medium">{{ Auth::user()->role ?? 'USER' }}</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-4 text-xs font-medium">
            @if (in_array(strtoupper(Auth::user()?->role), ['ADMIN']) || (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()))
                
                <!-- MENU ADMIN -->
                <div class="pl-4">
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}" 
                       class="w-full transition-all flex items-center px-4 py-2.5 font-semibold text-xs shadow-sm rounded-l-full rounded-r-none {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'bg-white text-[#138A73]' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-r-lg' }}">
                        <i class="fa-solid fa-table-columns w-4 mr-2"></i> Dashboard Admin
                    </a>
                </div>

                <!-- MASTER DATA -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                        <i class="fa-solid fa-database w-4 text-emerald-300"></i>
                        <span>Master Data</span>
                    </div>
                    <ul class="pl-4 space-y-1 text-white/90">
                        <li>
                            <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.users.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.users.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Data Kelola Akun
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.guru.index') ? route('admin.guru.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.guru.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.guru.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Data Guru
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.kelas.index') ? route('admin.kelas.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.kelas.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.kelas.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Data Kelas
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.mapel.index') ? route('admin.mapel.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.mapel.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.mapel.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Mata Pelajaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.jadwal.index') ? route('admin.jadwal.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.jadwal.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.jadwal.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Jadwal Mengajar
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.siswa.index') ? route('admin.siswa.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.siswa.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.siswa.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Data Siswa
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- PRESENSI -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                        <i class="fa-regular fa-calendar-check w-4 text-emerald-300"></i>
                        <span>Presensi</span>
                    </div>
                    <ul class="pl-4 space-y-1 text-white/90">
                        <li>
                            <a href="{{ Route::has('admin.absen') ? route('admin.absen') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.absen*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.absen*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Rekap Absensi Guru
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- PENCATATAN SPP -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                        <i class="fa-solid fa-receipt w-4 text-emerald-300"></i>
                        <span>Pencatatan SPP</span>
                    </div>
                    <ul class="pl-4 space-y-1 text-white/90">
                        <li>
                            <a href="{{ Route::has('admin.spp.index') ? route('admin.spp.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ (request()->routeIs('admin.spp*') && !request()->routeIs('admin.spp.audit')) ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ (request()->routeIs('admin.spp*') && !request()->routeIs('admin.spp.audit')) ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Catat SPP Bulanan
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('admin.spp.audit') ? route('admin.spp.audit') : '#' }}"
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.spp.audit') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.spp.audit') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Audit Trail SPP
                            </a>
                        </li>
                    </ul>
                </div>

            @else

                <!-- MENU GURU -->
                <div class="pl-4">
                    <a href="{{ Route::has('guru.dashboard') ? route('guru.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}" 
                       class="w-full transition-all flex items-center px-4 py-2.5 font-semibold text-xs shadow-sm rounded-l-full rounded-r-none {{ request()->routeIs('guru.dashboard') || request()->routeIs('dashboard') ? 'bg-white text-[#138A73]' : 'text-white/90 hover:text-white hover:bg-white/10 rounded-r-lg' }}">
                        <i class="fa-solid fa-house w-4 mr-2"></i> Tampilan Utama
                    </a>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                        <i class="fa-regular fa-circle-user w-4 text-emerald-300"></i>
                        <span>Informasi Guru</span>
                    </div>
                    <ul class="pl-4 space-y-1 text-white/90">
                        <li>
                            <a href="{{ Route::has('guru.absen') ? route('guru.absen') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('guru.absen') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.absen') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Presensi Saya
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                        <i class="fa-solid fa-chalkboard-user w-4 text-emerald-300"></i>
                        <span>Ruang Kelas</span>
                    </div>
                    <ul class="pl-4 space-y-1 text-white/90">
                        <li>
                            <a href="{{ Route::has('guru.absen-siswa') ? route('guru.absen-siswa') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('guru.absen-siswa') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.absen-siswa') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Absen Siswa
                            </a>
                        </li>
                        <li>
                            <a href="{{ Route::has('guru.rekap-siswa') ? route('guru.rekap-siswa') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('guru.rekap-siswa') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('guru.rekap-siswa') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Rekap Absen Siswa
                            </a>
                        </li>
                    </ul>
                </div>

            @endif

            <!-- Pengaturan Umum -->
            <div class="space-y-1 pt-3 border-t border-white/20">
                <div class="flex items-center space-x-2 px-6 py-1 text-emerald-200/90 font-bold text-[11px] uppercase tracking-wider">
                    <i class="fa-solid fa-gear w-4 text-emerald-300"></i>
                    <span>Pengaturan</span>
                </div>
                <ul class="pl-4 space-y-1 text-white/90">
                    <li>
                        <a href="{{ Route::has('profile.edit') ? route('profile.edit') : '#' }}" 
                           class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('profile.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('profile.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Profil Saya
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- TOMBOL LOGOUT -->
        <div class="px-4 pt-3 mt-4 border-t border-white/20">
            <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar?')" class="w-full text-left px-3 py-2 text-xs font-semibold text-rose-200 hover:text-white hover:bg-rose-600/30 rounded-lg flex items-center gap-2.5 transition-all">
                    <i class="fa-solid fa-right-from-bracket text-sm text-rose-300"></i>
                    <span>Keluar / Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Bottom Mascot Graphic (Berdasarkan Role User) -->
    <div class="pt-4 relative flex justify-center items-end mt-auto px-4">
        @if (Auth::check() && in_array(strtoupper(Auth::user()->role), ['ADMIN']))
            <!-- Maskot Khusus Admin (Bebas dari error parser) -->
            <img src="{{ asset('images/maskot_admin.png') }}" 
                 onerror="this.onerror=null; this.src='/images/maskot_admin.jpeg';" 
                 alt="Mascot Admin" 
                 class="w-36 max-h-44 h-auto object-contain drop-shadow-lg">
        @else
            <!-- Maskot Guru Cewe -->
            <img src="{{ asset('images/guru_cewe.png') }}" 
                 onerror="this.onerror=null; this.src='https://cdni.iconscout.com/illustration/premium/thumb/female-teacher-holding-book-and-pointing-finger-illustration-download-in-svg-png-gif-file-formats--woman-pointing-up-character-pack-people-illustrations-3798680.png';" 
                 alt="Mascot Teacher" 
                 class="w-36 max-h-44 h-auto object-contain drop-shadow-lg">
        @endif
    </div>
</aside>