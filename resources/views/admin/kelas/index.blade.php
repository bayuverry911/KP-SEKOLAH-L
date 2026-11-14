<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas - SMP Djojoredjo</title>
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
                        MASTER DATA • DATA KELAS
                    </h1>
                </div>
                <span class="px-3 py-1.5 bg-white rounded-xl shadow-sm border border-slate-200/60 text-xs font-semibold text-slate-600 shrink-0">
                    <i class="fa-solid fa-door-open text-[#19A389] mr-1"></i> Total: <b>{{ $kelases->count() }}</b> Kelas
                </span>
            </header>

            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-6xl mx-auto space-y-5 sm:space-y-6">

                    <!-- Hero Header Card -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Struktur Rombongan Belajar</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Master Data Kelas</h2>
                            <p class="text-xs text-white/80 mt-1">Kelola daftar tingkat dan ruang kelas aktif yang tersedia di SMP Djojoredjo</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-school"></i>
                            <span>{{ $kelases->count() }} Rombel Terdaftar</span>
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

                    @if($errors->any())
                        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl space-y-1 font-medium shadow-sm">
                            @foreach($errors->all() as $error)
                                <p><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">
                        <!-- Form Tambah Kelas -->
                        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xl border border-slate-100">
                            <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">Tambah Kelas Baru</h3>
                                    <p class="text-[11px] text-slate-400">Input nama atau tingkat kelas</p>
                                </div>
                            </div>

                            <form action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                        Nama / Tingkat Kelas <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" required
                                           placeholder="Contoh: 7A, 8B, 9C"
                                           class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                                <button type="submit"
                                        class="w-full py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl transition shadow-md flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kelas
                                </button>
                            </form>
                        </div>

                        <!-- Tabel Daftar Kelas -->
                        <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">Daftar Kelas</h3>
                                    <p class="text-[11px] text-slate-400">Seluruh kelas yang digunakan pada data siswa & jadwal</p>
                                </div>
                                <div class="relative w-full sm:w-60">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" id="cariKelas" onkeyup="filterTabel('cariKelas', 'tabelKelas')"
                                           placeholder="Cari nama kelas..."
                                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table id="tabelKelas" class="w-full text-xs text-left">
                                    <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                        <tr>
                                            <th class="p-3.5 sm:p-4 w-14 text-center">No</th>
                                            <th class="p-3.5 sm:p-4">Nama Kelas</th>
                                            <th class="p-3.5 sm:p-4 text-center w-24">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse($kelases as $kelas)
                                            <tr class="hover:bg-slate-50/90 transition">
                                                <td class="p-3.5 sm:p-4 font-semibold text-slate-400 text-center">{{ $loop->iteration }}</td>
                                                <td class="p-3.5 sm:p-4">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-[#0D6353] border border-emerald-200 font-extrabold text-xs">
                                                        <i class="fa-solid fa-door-open mr-1.5 text-[#19A389]"></i>{{ $kelas->nama_kelas }}
                                                    </span>
                                                </td>
                                                <td class="p-3.5 sm:p-4 text-center">
                                                    <form id="delete-kelas-{{ $kelas->id }}" action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                data-id="{{ $kelas->id }}"
                                                                data-name="{{ $kelas->nama_kelas }}"
                                                                onclick="confirmDeleteKelas(this)"
                                                                title="Hapus Kelas"
                                                                class="w-9 h-9 sm:w-8 sm:h-8 bg-rose-100 text-rose-600 hover:bg-rose-200 active:bg-rose-300 rounded-xl inline-flex items-center justify-center transition">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="p-8 text-center text-slate-400 italic">
                                                    <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                                    Belum ada data kelas tersimpan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        function filterTabel(inputId, tableId) {
            const keyword = document.getElementById(inputId).value.toLowerCase();
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            rows.forEach(row => {
                if (row.cells.length === 1) return;
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });
        }

        function confirmDeleteKelas(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Data Kelas?',
                text: `Apakah Anda yakin ingin menghapus kelas "${name}"? Data terkait mungkin ikut terpengaruh.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-kelas-${id}`).submit();
                }
            });
        }
    </script>
</body>
</html>