<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
            <div class="max-w-4xl mx-auto space-y-6">

                <!-- Header -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Pengaturan Profil Saya</h2>
                        <p class="text-xs text-slate-500">Kelola informasi pribadi dan keamanan akun Anda</p>
                    </div>
                    <span class="px-3 py-1 bg-[#24B9A1]/10 text-[#24B9A1] font-bold text-xs rounded-full uppercase border border-[#24B9A1]/20">
                        Role: {{ $user->role ?? 'User' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Kartu Ringkasan Akun (Kiri) -->
                    <div class="lg:col-span-1 bg-white p-6 rounded-3xl shadow-sm border border-slate-200 flex flex-col items-center text-center h-fit">
                        <div class="w-20 h-20 rounded-full bg-[#0C2320] text-white flex items-center justify-center text-2xl font-bold mb-3 shadow-md">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm mb-0.5">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 mb-3">{{ $user->email }}</p>
                        
                        <div class="w-full pt-4 border-t border-slate-100 text-left space-y-2 text-xs">
                            <div class="flex justify-between text-slate-600">
                                <span class="font-medium">Status Akun:</span>
                                <span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                            </div>
                            <div class="flex justify-between text-slate-600">
                                <span class="font-medium">Hak Akses:</span>
                                <span class="font-semibold text-slate-800 uppercase">{{ $user->role ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Pengaturan (Kanan) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Form Informasi Profil -->
                        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-id-card text-[#24B9A1]"></i> Informasi Pribadi
                            </h3>

                            @if (session('success_profile'))
                                <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>{{ session('success_profile') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 text-xs">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="block font-medium text-slate-600 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                    @error('name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block font-medium text-slate-600 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                    @error('email') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block font-medium text-slate-600 mb-1">No. Telepon / WhatsApp</label>
                                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon ?? '') }}" placeholder="0812xxxxxxxx" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="px-5 py-2.5 bg-[#24B9A1] hover:bg-[#1da18c] text-white rounded-xl font-semibold shadow-md transition">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Form Ubah Password -->
                        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-lock text-[#24B9A1]"></i> Ubah Kata Sandi
                            </h3>

                            @if (session('success_password'))
                                <div class="mb-4 p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>{{ session('success_password') }}</span>
                                </div>
                            @endif

                            <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4 text-xs">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block font-medium text-slate-600 mb-1">Kata Sandi Saat Ini <span class="text-rose-500">*</span></label>
                                    <input type="password" name="current_password" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                    @error('current_password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-slate-600 mb-1">Kata Sandi Baru <span class="text-rose-500">*</span></label>
                                        <input type="password" name="password" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                        @error('password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-medium text-slate-600 mb-1">Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span></label>
                                        <input type="password" name="password_confirmation" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#24B9A1]">
                                    </div>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-semibold shadow-md transition">
                                        Perbarui Kata Sandi
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>