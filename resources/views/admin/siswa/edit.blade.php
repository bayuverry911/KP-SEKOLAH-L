<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        DATA SISWA • EDIT DATA SISWA
                    </h1>
                </div>

                <a href="{{ route('admin.siswa.index') }}"
                   class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                    <span>Kembali</span>
                </a>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-2xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Master Data Peserta Didik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Edit Data Siswa</h2>
                            <p class="text-xs text-white/80 mt-1">Perbarui NISN, nama lengkap, kelas, atau jenis kelamin siswa</p>
                        </div>
                        <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                    </div>

                    <!-- FORM CARD -->
                    <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                        @if ($errors->any())
                            <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl">
                                <ul class="list-disc pl-4 space-y-1 font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    NISN <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nisn" value="{{ old('nisn', $siswa->nisn) }}" required placeholder="Contoh: 0051234567"
                                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-mono text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap Siswa <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama', $siswa->nama ?? $siswa->name) }}" required placeholder="Masukkan nama lengkap"
                                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <!-- KELAS DENGAN CEK DATA FLEXIBLE & DINAMIS -->
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Kelas <span class="text-rose-500">*</span>
                                </label>
                                <select name="kelas" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 font-bold focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Kelas --</option>

                                    @php
                                        $currentKelas = old('kelas', $siswa->kelas);
                                    @endphp

                                    @if(isset($kelases) && count($kelases) > 0)
                                        <!-- Opsi Dinamis dari Database Master Kelas -->
                                        @foreach($kelases as $k)
                                            @php
                                                $valKelas = $k->nama_kelas ?? $k->nama ?? $k;
                                                $isSelected = (
                                                    $currentKelas == $valKelas ||
                                                    strtolower(trim($currentKelas)) == strtolower(trim($valKelas)) ||
                                                    strtolower(trim(str_replace('kelas', '', $currentKelas))) == strtolower(trim(str_replace('kelas', '', $valKelas)))
                                                );
                                            @endphp
                                            <option value="{{ $valKelas }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ str_contains(strtolower($valKelas), 'kelas') ? $valKelas : 'Kelas ' . $valKelas }}
                                            </option>
                                        @endforeach
                                    @else
                                        <!-- Fallback Opsi Statis (Mendukung Format 'Kelas X' dan Angka 'X') -->
                                        @foreach(['7', '8', '9', '7A', '7B', '8A', '8B', '9A', '9B', 'Kelas 7', 'Kelas 8', 'Kelas 9'] as $opt)
                                            @php
                                                $isSelected = (
                                                    $currentKelas == $opt ||
                                                    strtolower(trim($currentKelas)) == strtolower(trim($opt)) ||
                                                    strtolower(trim(str_replace('kelas', '', $currentKelas))) == strtolower(trim(str_replace('kelas', '', $opt)))
                                                );
                                            @endphp
                                            <option value="{{ $opt }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ str_contains(strtolower($opt), 'kelas') ? $opt : 'Kelas ' . $opt }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Jenis Kelamin <span class="text-rose-500">*</span>
                                </label>
                                <select name="jenis_kelamin" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 font-semibold focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="pt-2 flex flex-col-reverse sm:flex-row gap-2.5">
                                <a href="{{ route('admin.siswa.index') }}"
                                   class="w-full sm:w-auto px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs text-center transition">
                                    Batal
                                </a>
                                <button type="submit"
                                        class="w-full flex-1 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Perbarui Data Siswa</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>

</body>
</html>