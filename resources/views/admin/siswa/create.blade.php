<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <div class="px-4 sm:px-8 py-8">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 max-w-2xl mx-auto space-y-6">
                    <div class="flex justify-between items-center border-b pb-4">
                        <h2 class="text-xl font-bold text-slate-900">Tambah Data Siswa</h2>
                        <a href="{{ route('admin.siswa.index') }}" class="text-xs text-slate-500 hover:text-slate-800">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    @if ($errors->any())
                        <div class="p-3 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl">
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.siswa.store') }}" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-medium text-slate-500 mb-1">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" required placeholder="Contoh: 0051234567" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Nama Lengkap Siswa</label>
                            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Kelas</label>
                            <select name="kelas" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->nama_kelas }}" {{ old('kelas') == $kelas->nama_kelas ? 'selected' : '' }}>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium text-slate-500 mb-1">Jenis Kelamin</label>
                            <select name="jenis_kelamin" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-[#24B9A1] hover:bg-[#1DA38D] text-white font-semibold rounded-xl text-xs shadow-md transition">
                            Simpan Data Siswa
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

</body>
</html>