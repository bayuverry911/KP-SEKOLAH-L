<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun Pengguna - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        
        <!-- PANGGIL SIDEBAR -->
        @include('layouts.partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            
            <!-- TOPBAR HEADER -->
            <header class="bg-white border-b px-8 py-4 flex justify-between items-center sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <button class="text-slate-500 hover:text-slate-700">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Kelola Akun Pengguna</h2>
                        <p class="text-xs text-slate-500">Tambah dan atur hak akses pengguna sistem</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-xs text-slate-500 border rounded-lg px-3 py-1.5 bg-slate-50">
                        <i class="fa-regular fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- BODY CONTENT FORM -->
            <div class="p-8">
                <div class="max-w-3xl mx-auto">
                    
                    <!-- Card Container -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        
                        <!-- Header Form -->
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-emerald-100 text-emerald-700 rounded-lg">
                                    <i class="fa-solid fa-user-plus text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800">Form Tambah Akun Baru</h3>
                                    <p class="text-xs text-slate-500">Isi data akun dengan lengkap di bawah ini</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:text-slate-700 flex items-center space-x-1">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Kembali</span>
                            </a>
                        </div>

                        <!-- Form Input -->
                        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Nama Lengkap -->
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-user text-sm"></i>
                                        </div>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition"
                                            placeholder="Masukkan nama lengkap">
                                    </div>
                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase mb-2">Alamat Email <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-envelope text-sm"></i>
                                        </div>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition"
                                            placeholder="contoh@sekolah.sch.id">
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Role / Hak Akses -->
                                <div>
                                    <label for="role" class="block text-xs font-semibold text-slate-700 uppercase mb-2">Role / Hak Akses <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-user-shield text-sm"></i>
                                        </div>
                                        <select id="role" name="role" required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition">
                                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin / Bendahara</option>
                                            <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                                        </select>
                                    </div>
                                    @error('role')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase mb-2">Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-lock text-sm"></i>
                                        </div>
                                        <input type="password" id="password" name="password" required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition"
                                            placeholder="Minimal 8 karakter">
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-key text-sm"></i>
                                        </div>
                                        <input type="password" id="password_confirmation" name="password_confirmation" required
                                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition"
                                            placeholder="Ketik ulang password">
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-200">
                                <a href="{{ route('admin.users.index') }}" 
                                    class="px-5 py-2.5 bg-slate-100 border border-slate-300 text-slate-700 font-medium text-xs rounded-lg hover:bg-slate-200 transition">
                                    Batal
                                </a>
                                <button type="submit" 
                                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center space-x-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Simpan Akun</span>
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