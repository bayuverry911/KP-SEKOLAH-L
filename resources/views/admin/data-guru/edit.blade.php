<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased">
    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
            <div class="max-w-2xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Edit Data Guru</h2>
                        <p class="text-xs text-slate-500">Perbarui informasi data guru terdaftar di SMP Djojoredjo</p>
                    </div>
                    <a href="{{ route('admin.guru.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-2 rounded-xl transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-rose-100 border border-rose-300 text-rose-700 text-xs rounded-xl">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">Hubungkan ke Akun Login Guru (Opsional)</label>
                        <select name="user_id" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                            <option value="">-- Tidak Terhubung ke Akun --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $guru->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">Nama Lengkap Guru <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $guru->nama) }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]" placeholder="Nama lengkap beserta gelar">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-slate-600 mb-1">NUPTK</label>
                            <input type="text" name="nuptk" value="{{ old('nuptk', $guru->nuptk ?? $guru->nip) }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]" placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan">
                        </div>
                        <div>
                            <label class="block font-medium text-slate-600 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="jenis_kelamin" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                        <select name="mata_pelajaran" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @if(isset($mapels) && count($mapels) > 0)
                                @foreach($mapels as $mapel)
                                    @php $namaMapel = $mapel->nama_mapel ?? $mapel->nama; @endphp
                                    <option value="{{ $namaMapel }}" {{ old('mata_pelajaran', $guru->mata_pelajaran) == $namaMapel ? 'selected' : '' }}>
                                        {{ $namaMapel }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Bahasa Indonesia" {{ old('mata_pelajaran', $guru->mata_pelajaran) == 'Bahasa Indonesia' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                <option value="Matematika" {{ old('mata_pelajaran', $guru->mata_pelajaran) == 'Matematika' ? 'selected' : '' }}>Matematika</option>
                                <option value="Bahasa Inggris" {{ old('mata_pelajaran', $guru->mata_pelajaran) == 'Bahasa Inggris' ? 'selected' : '' }}>Bahasa Inggris</option>
                                <option value="IPA" {{ old('mata_pelajaran', $guru->mata_pelajaran) == 'IPA' ? 'selected' : '' }}>IPA</option>
                            @endif
                        </select>
                    </div>

                    <!-- KELAS MENGAJAR MULTI SELECTION -->
                    <div>
                        <label class="block font-medium text-slate-600 mb-2">Kelas Mengajar (Bisa Pilih Lebih Dari Satu)</label>
                        @php
                            $savedKelas = old('kelas', $guru->kelas);
                            if (is_string($savedKelas)) {
                                $savedKelas = array_map('trim', explode(',', $savedKelas));
                            } elseif (!is_array($savedKelas)) {
                                $savedKelas = [];
                            }
                        @endphp
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            @if(isset($kelases) && count($kelases) > 0)
                                @foreach($kelases as $kelas)
                                    @php $namaKls = $kelas->nama_kelas ?? $kelas->nama; @endphp
                                    <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 bg-white p-2 rounded-lg border border-slate-200 hover:border-[#24B9A1] transition">
                                        <input type="checkbox" name="kelas[]" value="{{ $namaKls }}" {{ in_array($namaKls, $savedKelas) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#24B9A1] focus:ring-[#24B9A1] accent-[#24B9A1]">
                                        <span>{{ $namaKls }}</span>
                                    </label>
                                @endforeach
                            @else
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 bg-white p-2 rounded-lg border border-slate-200">
                                    <input type="checkbox" name="kelas[]" value="7A" {{ in_array('7A', $savedKelas) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#24B9A1] focus:ring-[#24B9A1] accent-[#24B9A1]">
                                    <span>7A</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 bg-white p-2 rounded-lg border border-slate-200">
                                    <input type="checkbox" name="kelas[]" value="8A" {{ in_array('8A', $savedKelas) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#24B9A1] focus:ring-[#24B9A1] accent-[#24B9A1]">
                                    <span>8A</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 bg-white p-2 rounded-lg border border-slate-200">
                                    <input type="checkbox" name="kelas[]" value="9A" {{ in_array('9A', $savedKelas) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#24B9A1] focus:ring-[#24B9A1] accent-[#24B9A1]">
                                    <span>9A</span>
                                </label>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-600 mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telepon" value="{{ old('no_telepon', $guru->no_telepon) }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]" placeholder="0812xxxxxxxx">
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <a href="{{ route('admin.guru.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-[#24B9A1] hover:bg-[#1da18c] text-white rounded-xl font-semibold shadow-md transition">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>