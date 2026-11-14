<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <!-- Top Bar -->
            <header class="bg-[#EAF0F6]/90 backdrop-blur-md sticky top-0 z-20 px-4 sm:px-8 py-3.5 flex justify-between items-center gap-3 border-b border-slate-200/50 lg:border-none">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="mobileSidebarBtn" aria-label="Buka Menu"
                        class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl bg-white shadow-sm border border-slate-200/60 transition shrink-0">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest truncate">
                        MASTER DATA • DATA GURU
                    </h1>
                </div>
                <span class="px-3 py-1.5 bg-white rounded-xl shadow-sm border border-slate-200/60 text-xs font-semibold text-slate-600 shrink-0">
                    <i class="fa-solid fa-chalkboard-user text-[#19A389] mr-1"></i> Total: <b>{{ $gurus->count() }}</b> Guru
                </span>
            </header>

            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- Hero Header Card -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Manajemen Tenaga Pendidik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Kelola Data Guru</h2>
                            <p class="text-xs text-white/80 mt-1">Daftar guru aktif, NUPTK, mata pelajaran, dan kelas yang diampu di SMP Djojoredjo</p>
                        </div>
                        <a href="{{ route('admin.guru.create') }}"
                           class="w-full sm:w-auto bg-white text-[#0D6353] hover:bg-emerald-50 active:bg-emerald-100 px-5 py-3 rounded-2xl text-xs font-extrabold inline-flex items-center justify-center gap-2 shadow-lg transition shrink-0">
                            <i class="fa-solid fa-plus"></i> Tambah Guru
                        </a>
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

                    <!-- Card Daftar Utama -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Toolbar Pencarian -->
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Daftar Guru Terdaftar</h3>
                                <p class="text-[11px] text-slate-400">Gunakan kolom pencarian untuk menyaring nama, NUPTK, mapel, atau kelas</p>
                            </div>
                            <div class="relative w-full sm:w-72">
                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                <input type="text" id="cariGuru" onkeyup="filterGuru()"
                                       placeholder="Cari nama, NUPTK, mapel..."
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>
                        </div>

                        <!-- TAMPILAN HP (MOBILE CARD VIEW: < 768px) -->
                        <div id="mobileGuruList" class="block md:hidden divide-y divide-slate-100">
                            @forelse($gurus as $guru)
                                <div class="guru-card p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] font-extrabold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(mb_substr($guru->nama, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm text-slate-800 truncate">{{ $guru->nama }}</p>
                                                <p class="text-[11px] font-mono text-emerald-700 font-semibold">
                                                    NUPTK: {{ $guru->nuptk ?? $guru->nip ?? '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            @if($guru->jenis_kelamin == 'L' || $guru->jenis_kelamin == 'Laki-laki')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700">
                                                    <i class="fa-solid fa-mars mr-1"></i>Laki-laki
                                                </span>
                                            @elseif($guru->jenis_kelamin == 'P' || $guru->jenis_kelamin == 'Perempuan')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                    <i class="fa-solid fa-venus mr-1"></i>Perempuan
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-2xl text-xs">
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-slate-400">Mata Pelajaran</p>
                                            <p class="font-semibold text-slate-700 mt-0.5">{{ $guru->mata_pelajaran ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold text-slate-400">No. Telepon</p>
                                            <p class="font-medium text-slate-600 mt-0.5">{{ $guru->no_telepon ?? '-' }}</p>
                                        </div>
                                        <div class="col-span-2 pt-1 border-t border-slate-200/60">
                                            <p class="text-[10px] uppercase font-bold text-slate-400 mb-1">Kelas Mengajar</p>
                                            @if(!empty($guru->kelas))
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(explode(',', $guru->kelas) as $kls)
                                                        <span class="bg-emerald-50 text-[#0D6353] border border-emerald-200 px-2 py-0.5 rounded-lg font-bold text-[11px]">
                                                            {{ trim($kls) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Belum diatur</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-1">
                                        <a href="{{ route('admin.guru.edit', $guru->id) }}"
                                           class="flex-1 py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <button type="button"
                                                data-id="{{ $guru->id }}"
                                                data-name="{{ $guru->nama }}"
                                                onclick="confirmDeleteGuru(this)"
                                                class="flex-1 py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada data guru terdaftar.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (TABLE VIEW: >= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table id="tabelGuru" class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 w-12 text-center">No</th>
                                        <th class="p-4">Nama Guru</th>
                                        <th class="p-4">NUPTK</th>
                                        <th class="p-4 whitespace-nowrap">Jenis Kelamin</th>
                                        <th class="p-4">Mata Pelajaran</th>
                                        <th class="p-4 whitespace-nowrap">No Telepon</th>
                                        <th class="p-4">Kelas Mengajar</th>
                                        <th class="p-4 text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($gurus as $guru)
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="p-4 font-semibold text-center text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="p-4">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#0D6353] font-extrabold flex items-center justify-center text-xs shrink-0">
                                                        {{ strtoupper(mb_substr($guru->nama, 0, 1)) }}
                                                    </div>
                                                    <span class="font-bold text-slate-800">{{ $guru->nama }}</span>
                                                </div>
                                            </td>
                                            <td class="p-4 font-mono text-emerald-700 font-semibold whitespace-nowrap">{{ $guru->nuptk ?? $guru->nip ?? '-' }}</td>
                                            <td class="p-4 whitespace-nowrap">
                                                @if($guru->jenis_kelamin == 'L' || $guru->jenis_kelamin == 'Laki-laki')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700">
                                                        <i class="fa-solid fa-mars mr-1"></i>Laki-laki
                                                    </span>
                                                @elseif($guru->jenis_kelamin == 'P' || $guru->jenis_kelamin == 'Perempuan')
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                                        <i class="fa-solid fa-venus mr-1"></i>Perempuan
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="p-4 font-semibold text-slate-700">{{ $guru->mata_pelajaran ?? '-' }}</td>
                                            <td class="p-4 text-slate-600 whitespace-nowrap">{{ $guru->no_telepon ?? '-' }}</td>
                                            <td class="p-4">
                                                @if(!empty($guru->kelas))
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach(explode(',', $guru->kelas) as $kls)
                                                            <span class="bg-emerald-50 text-[#0D6353] border border-emerald-200 px-2.5 py-0.5 rounded-lg font-bold text-[11px]">
                                                                {{ trim($kls) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic">-</span>
                                                @endif
                                            </td>
                                            <td class="p-4 text-center whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a href="{{ route('admin.guru.edit', $guru->id) }}" title="Edit Data"
                                                       class="w-8 h-8 bg-amber-100 text-amber-600 hover:bg-amber-200 rounded-xl inline-flex items-center justify-center transition">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <form id="delete-guru-{{ $guru->id }}" action="{{ route('admin.guru.destroy', $guru->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                data-id="{{ $guru->id }}"
                                                                data-name="{{ $guru->nama }}"
                                                                onclick="confirmDeleteGuru(this)"
                                                                title="Hapus Data"
                                                                class="w-8 h-8 bg-rose-100 text-rose-600 hover:bg-rose-200 rounded-xl inline-flex items-center justify-center transition">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="p-8 text-center text-slate-400 italic">
                                                <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                                Belum ada data guru terdaftar.
                                            </td>
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

    <script>
        function filterGuru() {
            const keyword = document.getElementById('cariGuru').value.toLowerCase();

            // Filter baris tabel (Tablet & Desktop)
            const rows = document.querySelectorAll('#tabelGuru tbody tr');
            rows.forEach(row => {
                if (row.cells.length === 1) return;
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });

            // Filter kartu (HP)
            const cards = document.querySelectorAll('#mobileGuruList .guru-card');
            cards.forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });
        }

        function confirmDeleteGuru(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Data Guru?',
                text: `Apakah Anda yakin ingin menghapus data "${name}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-guru-${id}`).submit();
                }
            });
        }
    </script>
</body>
</html>