<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        MASTER DATA • TAMBAH GURU
                    </h1>
                </div>
                <a href="{{ route('admin.guru.index') }}"
                   class="px-3.5 py-2 bg-white hover:bg-slate-50 rounded-xl shadow-sm border border-slate-200/60 text-xs font-bold text-slate-600 inline-flex items-center gap-1.5 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[#19A389]"></i> Kembali
                </a>
            </header>

            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-3xl mx-auto space-y-5 sm:space-y-6">

                    <!-- Hero Header Card -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Formulir Tenaga Pendidik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Tambah Data Guru</h2>
                            <p class="text-xs text-white/80 mt-0.5">Lengkapi biodata, mata pelajaran, dan kelas yang diampu</p>
                        </div>
                    </div>

                    <!-- Card Form -->
                    <div class="bg-white p-5 sm:p-8 rounded-3xl shadow-xl border border-slate-100">
                        @if ($errors->any())
                            <div class="mb-5 p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl">
                                <p class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Periksa kembali isian berikut:</p>
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.guru.store') }}" method="POST" class="space-y-4 sm:space-y-5">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Hubungkan ke Akun Login Guru <span class="text-slate-400 font-normal normal-case">(Opsional)</span>
                                </label>
                                <select name="user_id"
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Akun Guru --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap Guru <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama') }}" required
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition"
                                    placeholder="Nama lengkap beserta gelar">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">NUPTK</label>
                                    <input type="text" name="nuptk" value="{{ old('nuptk', old('nip')) }}"
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-mono text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition"
                                        placeholder="Nomor Unik Pendidik">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="jenis_kelamin" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                        Mata Pelajaran <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="mata_pelajaran" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @if(isset($mapels) && count($mapels) > 0)
                                            @foreach($mapels as $mapel)
                                                @php $namaMapel = $mapel->nama_mapel ?? $mapel->nama; @endphp
                                                <option value="{{ $namaMapel }}" {{ old('mata_pelajaran') == $namaMapel ? 'selected' : '' }}>
                                                    {{ $namaMapel }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="Bahasa Indonesia" {{ old('mata_pelajaran') == 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="Matematika" {{ old('mata_pelajaran') == 'Matematika' ? 'selected' : '' }}>Matematika</option>
                                            <option value="Bahasa Inggris" {{ old('mata_pelajaran') == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                                            <option value="IPA" {{ old('mata_pelajaran') == 'IPA' ? 'selected' : '' }}>IPA</option>
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">No. Telepon / WhatsApp</label>
                                    <input type="tel" name="no_telepon" value="{{ old('no_telepon') }}"
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition"
                                        placeholder="0812xxxxxxxx">
                                </div>
                            </div>

                            <!-- KELAS MENGAJAR: MENANGANI BANYAK PILIHAN KELAS -->
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-2">
                                    Kelas Mengajar <span class="text-slate-400 font-normal normal-case">(Bisa Pilih Lebih Dari Satu)</span>
                                </label>
                                @php
                                    $oldKelas = old('kelas', []);
                                    if (is_string($oldKelas)) {
                                        $oldKelas = array_map('trim', explode(',', $oldKelas));
                                    }
                                @endphp
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 p-3.5 bg-slate-50 border border-slate-200 rounded-2xl">
                                    @if(isset($kelases) && count($kelases) > 0)
                                        @foreach($kelases as $kelas)
                                            @php $namaKls = $kelas->nama_kelas ?? $kelas->nama; @endphp
                                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200 hover:border-[#19A389] transition select-none">
                                                <input type="checkbox" name="kelas[]" value="{{ $namaKls }}" {{ in_array($namaKls, $oldKelas) ? 'checked' : '' }}
                                                    class="w-4 h-4 rounded text-[#19A389] focus:ring-[#19A389] accent-[#19A389]">
                                                <span>{{ $namaKls }}</span>
                                            </label>
                                        @endforeach
                                    @else
                                        @foreach(['7A', '8A', '9A'] as $defaultKls)
                                            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200 hover:border-[#19A389] transition select-none">
                                                <input type="checkbox" name="kelas[]" value="{{ $defaultKls }}" {{ in_array($defaultKls, $oldKelas) ? 'checked' : '' }}
                                                    class="w-4 h-4 rounded text-[#19A389] focus:ring-[#19A389] accent-[#19A389]">
                                                <span>{{ $defaultKls }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 sm:gap-3">
                                <a href="{{ route('admin.guru.index') }}"
                                    class="w-full sm:w-auto text-center px-5 py-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="w-full sm:w-auto justify-center px-6 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white rounded-xl text-xs font-extrabold shadow-md transition inline-flex items-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data
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