<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Pelajaran - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
            <div class="max-w-5xl mx-auto space-y-6">
                <!-- Header Page -->
                <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Master Data Mata Pelajaran</h2>
                        <p class="text-xs text-slate-500">Kelola kurikulum mata pelajaran yang diajarkan di SMP Djojoredjo</p>
                    </div>
                </div>

                <!-- Alert Success -->
                @if(session('success'))
                    <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Alert Error Validation -->
                @if($errors->any())
                    <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl space-y-1">
                        @foreach($errors->all() as $error)
                            <p><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Form Tambah Mapel -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
                        <h3 class="font-bold text-slate-800 text-sm mb-4">Tambah Mapel Baru</h3>
                        <form action="{{ route('admin.mapel.store') }}" method="POST" class="space-y-4 text-xs">
                            @csrf
                            <div>
                                <label class="block font-medium text-slate-600 mb-1">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_mapel" value="{{ old('nama_mapel') }}" required placeholder="Contoh: Bahasa Indonesia" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-[#24B9A1] hover:bg-[#1da18c] text-white font-semibold rounded-xl transition shadow-sm">
                                <i class="fa-solid fa-plus mr-1"></i> Simpan Mapel
                            </button>
                        </form>
                    </div>

                    <!-- Tabel Daftar Mapel -->
                    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <h3 class="font-bold text-slate-800 text-sm mb-4">Daftar Mata Pelajaran</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-[#0C2320] text-white uppercase font-bold">
                                    <tr>
                                        <th class="p-3 rounded-l-lg w-12 text-center">No</th>
                                        <th class="p-3">Mata Pelajaran</th>
                                        <th class="p-3 text-center rounded-r-lg w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($mapels as $index => $mapel)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="p-3 font-semibold text-slate-600 text-center">{{ $loop->iteration }}</td>
                                            <td class="p-3 font-bold text-slate-800">{{ $mapel->nama_mapel }}</td>
                                            <td class="p-3 text-center">
                                                <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus Mapel" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-6 text-center text-slate-400 italic">Belum ada data mata pelajaran tersimpan.</td>
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
</body>
</html>