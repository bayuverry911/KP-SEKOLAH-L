<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">DATA SISWA</h1>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 mt-4 space-y-6">

                @if(session('success'))
                    <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-graduate text-[#24B9A1]"></i> Daftar Siswa
                        </h2>

                        <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                            <form method="GET" action="{{ route('admin.siswa.index') }}" class="flex items-center gap-2 text-xs">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NISN / Nama..." class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                <button type="submit" class="p-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition">
                                    <i class="fa-solid fa-search"></i>
                                </button>
                            </form>

                            <a href="{{ route('admin.siswa.create') }}" class="px-4 py-2.5 bg-[#24B9A1] hover:bg-[#1DA38D] text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center gap-1.5">
                                <i class="fa-solid fa-plus"></i> Tambah Siswa
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="p-3">#</th>
                                    <th class="p-3">NISN</th>
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3">Jenis Kelamin</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($siswas as $index => $siswa)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3">{{ $siswas->firstItem() + $index }}</td>
                                        <td class="p-3 font-mono font-semibold text-slate-700">{{ $siswa->nisn }}</td>
                                        <td class="p-3 font-semibold text-slate-900">{{ $siswa->nama }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-700 uppercase">
                                                {{ $siswa->kelas }}
                                            </span>
                                        </td>
                                        <td class="p-3">{{ $siswa->jenis_kelamin }}</td>
                                        <td class="p-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('admin.siswa.edit', $siswa->id) }}" class="p-2 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-100 text-rose-700 rounded-lg hover:bg-rose-200 transition">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-slate-400">Belum ada data siswa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $siswas->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>