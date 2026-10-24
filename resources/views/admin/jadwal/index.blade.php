<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Mengajar - SMP Djojoredjo</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

    <!-- OVERLAY BACKGROUND UNTUK SIDEBAR HP -->
    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        
        <!-- SIDEBAR ADMIN -->
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
                        {{ Auth::user()->role ?? 'ADMIN' }}
                    </span>
                </div>

                <!-- User Info Profile -->
                <div class="mx-4 flex items-center space-x-3 p-2.5 bg-white/10 border border-white/20 rounded-xl mb-6 shadow-inner backdrop-blur-sm">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h2 class="font-semibold text-xs text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</h2>
                        <p class="text-[10px] text-emerald-200 uppercase font-medium">{{ Auth::user()->role ?? 'ADMIN' }}</p>
                    </div>
                </div>

                <!-- Navigation Menu -->
                <nav class="space-y-4 text-xs font-medium">
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
                            <a href="{{ Route::has('admin.kalender.index') ? route('admin.kalender.index') : '#' }}" 
                               class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.kalender.*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.kalender.*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Kalender Akademik
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
                                   class="hover:text-white flex items-center gap-2 px-4 py-2 transition-all rounded-l-full rounded-r-none {{ request()->routeIs('admin.spp*') ? 'bg-white text-[#138A73] font-bold shadow' : 'hover:bg-white/10 rounded-r-lg' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('admin.spp*') ? 'bg-[#138A73]' : 'bg-white/80' }}"></span> Catat SPP Bulanan
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- PENGATURAN -->
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

            <!-- Bottom Mascot Graphic -->
            <div class="pt-4 relative flex justify-center items-end mt-auto px-4">
                <img src="{{ asset('images/guru_cewe.png') }}" 
                     onerror="this.onerror=null; this.src='https://cdni.iconscout.com/illustration/premium/thumb/female-teacher-holding-book-and-pointing-finger-illustration-download-in-svg-png-gif-file-formats--woman-pointing-up-character-pack-people-illustrations-3798680.png';" 
                     alt="Mascot Teacher" class="w-36 max-h-44 h-auto object-contain drop-shadow-lg">
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            
            <!-- TOPBAR HEADER -->
            <header class="bg-transparent px-4 sm:px-8 pt-6 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">KELOLA JADWAL MENGAJAR</h1>
                </div>

                <!-- TOMBOL BACK / KEMBALI -->
                <div>
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}" 
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-4 py-2 rounded-xl shadow-sm border border-slate-200 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 py-4">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold text-slate-800">Kelola Jadwal Mengajar Guru</h2>
                    </div>

                    @if(session('success'))
                        <div class="p-3 mb-4 bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div class="p-3 mb-4 bg-rose-100 border border-rose-200 text-rose-700 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif
                    <!-- FORM INPUT JADWAL BARU -->
                    <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs mb-8 p-5 bg-slate-50 border border-slate-100 rounded-xl">
                        @csrf
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Guru</label>
                            <select name="guru_id" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama ?? $g->nama_guru }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Kelas</label>
                            <select name="kelas" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->nama_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Mata Pelajaran</label>
                            <select name="mapel" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapels as $m)
                                    <option value="{{ $m->nama_mapel }}">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Hari</label>
                            <select name="hari" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Jam Masuk</label>
                            <input type="time" name="jam_masuk" value="07:00" required class="w-full p-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                        </div>
                        <div>
                            <label class="block font-bold mb-1 text-slate-700">Jam Selesai</label>
                            <input type="time" name="jam_selesai" value="08:30" required class="w-full p-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-[#19A389]">
                        </div>
                        <div class="md:col-span-3 pt-2">
                            <button type="submit" class="w-full py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Jadwal Mengajar</span>
                            </button>
                        </div>
                    </form>

                    <!-- TABEL LIST JADWAL -->
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                <tr>
                                    <th class="p-3">Guru</th>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3">Mapel</th>
                                    <th class="p-3">Hari</th>
                                    <th class="p-3">Jam Masuk</th>
                                    <th class="p-3">Jam Selesai</th>
                                    <th class="p-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($jadwals as $j)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-semibold text-slate-800">{{ $j->nama_guru }}</td>
                                        <td class="p-3">{{ $j->kelas }}</td>
                                        <td class="p-3">{{ $j->mapel }}</td>
                                        <td class="p-3">{{ $j->hari }}</td>
                                        <td class="p-3 font-bold text-emerald-600">{{ $j->jam_masuk }}</td>
                                        <td class="p-3 font-bold text-rose-600">{{ $j->jam_selesai }}</td>
                                        <td class="p-3 whitespace-nowrap">
                                            <button type="button"
                                                    class="btn-pengganti mr-3 text-amber-600 hover:text-amber-800 font-semibold inline-flex items-center gap-1"
                                                    data-url="{{ route('admin.jadwal.pengganti.store', $j->id) }}"
                                                    data-info="{{ $j->kelas }} - {{ $j->mapel }} ({{ $j->hari }}, {{ substr($j->jam_masuk, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}) - {{ $j->nama_guru }}"
                                                    data-hari="{{ $j->hari }}"
                                                    data-guru="{{ $j->guru_id }}">
                                                <i class="fa-solid fa-user-clock text-xs"></i> Pengganti
                                            </button>
                                            <form action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-trash text-xs"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-slate-400 italic">Belum ada data jadwal mengajar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- DAFTAR GURU PENGGANTI -->
                    <div class="mt-8">
                        <h3 class="text-sm font-bold text-slate-800 mb-1 flex items-center gap-2">
                            <i class="fa-solid fa-user-clock text-amber-500"></i> Guru Pengganti yang Sudah Di-plot
                        </h3>
                        <p class="text-[11px] text-slate-500 mb-3">Jadwal tampil otomatis di dashboard guru pengganti pada tanggal yang dipilih. Guru asli tidak dapat mengabsen jadwal tersebut pada tanggal itu.</p>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-amber-500 text-white uppercase font-bold">
                                    <tr>
                                        <th class="p-3">Tanggal</th>
                                        <th class="p-3">Kelas / Mapel</th>
                                        <th class="p-3">Jam</th>
                                        <th class="p-3">Guru Asli</th>
                                        <th class="p-3">Guru Pengganti</th>
                                        <th class="p-3">Keterangan</th>
                                        <th class="p-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($penggantis as $g)
                                        <tr class="hover:bg-slate-50">
                                            <td class="p-3 font-semibold text-slate-800">{{ $g->tanggal->format('d/m/Y') }}</td>
                                            <td class="p-3">{{ $g->jadwal->kelas ?? '-' }} - {{ $g->jadwal->mapel ?? '-' }}</td>
                                            <td class="p-3">{{ $g->jadwal ? substr($g->jadwal->jam_masuk, 0, 5) . ' - ' . substr($g->jadwal->jam_selesai, 0, 5) : '-' }}</td>
                                            <td class="p-3">{{ $g->jadwal->nama_guru ?? '-' }}</td>
                                            <td class="p-3 font-bold text-amber-700">{{ $g->nama_guru }}</td>
                                            <td class="p-3">{{ $g->keterangan ?: '-' }}</td>
                                            <td class="p-3">
                                                <form action="{{ route('admin.jadwal.pengganti.destroy', $g->id) }}" method="POST" onsubmit="return confirm('Batalkan penugasan guru pengganti ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-xmark text-xs"></i> Batalkan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="p-4 text-center text-slate-400 italic">Belum ada guru pengganti yang di-plot.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL PLOT GURU PENGGANTI -->
    <div id="modalPengganti" class="hidden fixed inset-0 z-[60] bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl w-full max-w-md">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Plot Guru Pengganti</h3>
            <p id="infoPengganti" class="text-xs text-slate-500 mb-4"></p>

            <form id="formPengganti" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Tanggal <span id="hintHari" class="font-normal text-slate-400"></span></label>
                    <input type="date" name="tanggal" id="tanggalPengganti" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Guru Pengganti</label>
                    <select name="guru_id" id="guruPengganti" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->nama ?? $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Keterangan (opsional)</label>
                    <input type="text" name="keterangan" maxlength="255" placeholder="Contoh: Guru asli sakit" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="btnBatalPengganti" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#19A389] text-white rounded-xl font-semibold hover:bg-[#138A73] transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== MODAL GURU PENGGANTI =====
        (function () {
            const modal = document.getElementById('modalPengganti');
            const form = document.getElementById('formPengganti');
            const tgl = document.getElementById('tanggalPengganti');
            const guruSel = document.getElementById('guruPengganti');
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            let hariJadwal = '';

            const today = new Date();
            tgl.min = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

            document.querySelectorAll('.btn-pengganti').forEach(btn => {
                btn.addEventListener('click', () => {
                    form.action = btn.dataset.url;
                    hariJadwal = btn.dataset.hari;
                    document.getElementById('infoPengganti').textContent = btn.dataset.info;
                    document.getElementById('hintHari').textContent = '(harus hari ' + hariJadwal + ')';
                    tgl.value = '';
                    Array.from(guruSel.options).forEach(o => { o.hidden = (o.value !== '' && o.value === btn.dataset.guru); });
                    guruSel.value = '';
                    modal.classList.remove('hidden');
                });
            });

            tgl.addEventListener('change', () => {
                if (!tgl.value) return;
                const nama = hari[new Date(tgl.value + 'T00:00:00').getDay()];
                tgl.setCustomValidity(nama === hariJadwal ? '' : 'Tanggal ini hari ' + nama + ', jadwal hari ' + hariJadwal + '.');
                tgl.reportValidity();
            });

            document.getElementById('btnBatalPengganti').addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });
        })();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>