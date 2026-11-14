<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        MASTER DATA • DATA SISWA
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
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Master Data Peserta Didik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Data Siswa SMP Djojoredjo</h2>
                            <p class="text-xs text-white/80 mt-1">Kelola biodata siswa, NISN, penempatan kelas, dan impor data massal</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>Total: {{ $siswas->total() }} Siswa</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- CARD DAFTAR SISWA -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Header Filter & Action -->
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Daftar Siswa Terdaftar</h3>
                                    <p class="text-[11px] text-slate-400">Menampilkan {{ $siswas->count() }} dari {{ $siswas->total() }} siswa</p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                <form method="GET" action="{{ route('admin.siswa.index') }}" class="flex items-center gap-2">
                                    <div class="relative flex-1 sm:w-60">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                        <input type="text" name="search" value="{{ request('search') }}"
                                               placeholder="Cari NISN / Nama..."
                                               class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    </div>
                                    <button type="submit" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shrink-0" title="Cari">
                                        <i class="fa-solid fa-search"></i>
                                    </button>
                                    @if(request('search'))
                                        <a href="{{ route('admin.siswa.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition shrink-0" title="Reset Pencarian">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    @endif
                                </form>

                                <a href="{{ route('admin.siswa.create') }}"
                                   class="px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 shrink-0">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Siswa</span>
                                </a>
                            </div>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($siswas as $siswa)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-slate-900 truncate">{{ $siswa->nama }}</p>
                                            <p class="text-xs font-mono text-slate-500 mt-0.5">NISN: <b class="text-slate-700">{{ $siswa->nisn }}</b></p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-[#0D6353] uppercase shrink-0">
                                            {{ $siswa->kelas }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl">
                                        <span>Jenis Kelamin:</span>
                                        <span class="font-semibold text-slate-800">{{ $siswa->jenis_kelamin }}</span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 pt-0.5">
                                        <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                                           class="py-2 px-3 bg-amber-100 text-amber-800 hover:bg-amber-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <form id="delete-siswa-mobile-{{ $siswa->id }}" action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    data-form="delete-siswa-mobile-{{ $siswa->id }}"
                                                    data-nama="{{ $siswa->nama }}"
                                                    onclick="confirmDeleteSiswa(this)"
                                                    class="w-full py-2 px-3 bg-rose-100 text-rose-700 hover:bg-rose-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-user text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada data siswa.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 w-14 text-center">No</th>
                                        <th class="p-4">NISN</th>
                                        <th class="p-4">Nama Siswa</th>
                                        <th class="p-4">Kelas</th>
                                        <th class="p-4">Jenis Kelamin</th>
                                        <th class="p-4 text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($siswas as $index => $siswa)
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="p-4 text-center font-semibold text-slate-400">{{ $siswas->firstItem() + $index }}</td>
                                            <td class="p-4 font-mono font-bold text-slate-700">{{ $siswa->nisn }}</td>
                                            <td class="p-4 font-bold text-slate-800">{{ $siswa->nama }}</td>
                                            <td class="p-4">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-[#0D6353] uppercase">
                                                    {{ $siswa->kelas }}
                                                </span>
                                            </td>
                                            <td class="p-4">{{ $siswa->jenis_kelamin }}</td>
                                            <td class="p-4 text-center">
                                                <div class="inline-flex items-center justify-center gap-1.5">
                                                    <a href="{{ route('admin.siswa.edit', $siswa->id) }}"
                                                       title="Edit Data Siswa"
                                                       class="w-8 h-8 bg-amber-100 text-amber-700 hover:bg-amber-200 rounded-xl inline-flex items-center justify-center transition">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <form id="delete-siswa-{{ $siswa->id }}" action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                data-form="delete-siswa-{{ $siswa->id }}"
                                                                data-nama="{{ $siswa->nama }}"
                                                                onclick="confirmDeleteSiswa(this)"
                                                                title="Hapus Data Siswa"
                                                                class="w-8 h-8 bg-rose-100 text-rose-600 hover:bg-rose-200 rounded-xl inline-flex items-center justify-center transition">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                                Belum ada data siswa.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($siswas->hasPages())
                            <div class="p-4 sm:px-6 border-t border-slate-100">
                                {{ $siswas->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDeleteSiswa(btn) {
            const formId = btn.getAttribute('data-form');
            const nama = btn.getAttribute('data-nama');

            Swal.fire({
                title: 'Hapus Data Siswa?',
                text: `Apakah Anda yakin ingin menghapus data siswa "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>