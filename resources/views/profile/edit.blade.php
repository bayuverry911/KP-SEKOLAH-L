<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - SMP Djojoredjo</title>
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

    @php
        $inisial = collect(explode(' ', trim($user->name ?? 'U')))
            ->filter()
            ->take(2)
            ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('');
    @endphp

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
                        AKUN • PENGATURAN PROFIL
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="{{ Route::has('admin.dashboard') && ($user->role ?? '') === 'admin' ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition">
                        <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-6xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Manajemen Akun Pengguna</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Pengaturan Profil Saya</h2>
                            <p class="text-xs text-white/80 mt-1">Kelola informasi pribadi, kontak aktif, dan keamanan kata sandi akun Anda</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-user-shield"></i>
                            <span>Role: {{ $user->role ?? 'User' }}</span>
                        </div>
                    </div>

                    <!-- GRID KONTEN PROFIL -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 items-start">

                        <!-- KARTU RINGKASAN AKUN (KIRI) -->
                        <div class="lg:col-span-1 bg-white p-6 rounded-3xl shadow-xl border border-slate-100 flex flex-col items-center text-center">
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#19A389] to-[#0D6353] text-white flex items-center justify-center text-2xl font-extrabold mb-3.5 shadow-lg tracking-wide">
                                {{ $inisial ?: 'U' }}
                            </div>
                            <h3 class="font-extrabold text-slate-900 text-base">{{ $user->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5 break-all">{{ $user->email }}</p>

                            <span class="mt-3 px-3 py-1 bg-teal-50 text-[#138A73] font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-teal-100">
                                {{ $user->role ?? 'User' }}
                            </span>

                            <div class="w-full mt-5 pt-4 border-t border-slate-100 text-left space-y-3 text-xs">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="font-medium text-slate-400">Status Akun</span>
                                    <span class="text-emerald-600 font-bold inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Aktif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="font-medium text-slate-400">Hak Akses</span>
                                    <span class="font-bold text-slate-800 uppercase">{{ $user->role ?? '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span class="font-medium text-slate-400">No. Telepon</span>
                                    <span class="font-semibold text-slate-700">{{ $user->no_telepon ?: '-' }}</span>
                                </div>
                                @if(!empty($user->created_at))
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span class="font-medium text-slate-400">Bergabung</span>
                                        <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- FORM PENGATURAN (KANAN) -->
                        <div class="lg:col-span-2 space-y-5 sm:space-y-6">

                            <!-- FORM INFORMASI PROFIL -->
                            <div class="bg-white p-5 sm:p-7 rounded-3xl shadow-xl border border-slate-100">
                                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0">
                                        <i class="fa-solid fa-id-card"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Informasi Pribadi</h3>
                                        <p class="text-[11px] sm:text-xs text-slate-400">Perbarui nama lengkap, alamat email, dan nomor WhatsApp aktif</p>
                                    </div>
                                </div>

                                @if (session('success_profile'))
                                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                                        <span>{{ session('success_profile') }}</span>
                                    </div>
                                @endif

                                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PATCH')

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                            Nama Lengkap <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <i class="fa-solid fa-user text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                                   class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        </div>
                                        @error('name')
                                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                                Alamat Email <span class="text-rose-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <i class="fa-solid fa-envelope text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                                       class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                            </div>
                                            @error('email')
                                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                                No. Telepon / WhatsApp
                                            </label>
                                            <div class="relative">
                                                <i class="fa-brands fa-whatsapp text-slate-400 text-sm absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                                <input type="text" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon ?? '') }}" placeholder="0812xxxxxxxx"
                                                       class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                            </div>
                                            @error('no_telepon')
                                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button type="submit"
                                                class="w-full sm:w-auto px-6 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white rounded-xl text-xs font-extrabold shadow-md transition inline-flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                            <span>Simpan Perubahan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- FORM UBAH PASSWORD -->
                            <div class="bg-white p-5 sm:p-7 rounded-3xl shadow-xl border border-slate-100">
                                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-base shrink-0">
                                        <i class="fa-solid fa-lock"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Ubah Kata Sandi</h3>
                                        <p class="text-[11px] sm:text-xs text-slate-400">Gunakan kombinasi kata sandi yang kuat untuk menjaga keamanan akun</p>
                                    </div>
                                </div>

                                @if (session('success_password'))
                                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                                        <span>{{ session('success_password') }}</span>
                                    </div>
                                @endif

                                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <i class="fa-solid fa-key text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                            <input type="password" name="current_password" id="current_password" required
                                                   class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                            <button type="button" data-target="current_password" aria-label="Tampilkan kata sandi"
                                                    class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')
                                            <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                                Kata Sandi Baru <span class="text-rose-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <i class="fa-solid fa-lock text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                                <input type="password" name="password" id="new_password" required
                                                       class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                                <button type="button" data-target="new_password" aria-label="Tampilkan kata sandi"
                                                        class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                                Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <i class="fa-solid fa-shield-halved text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                                <input type="password" name="password_confirmation" id="confirm_password" required
                                                       class="w-full pl-9 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                                <button type="button" data-target="confirm_password" aria-label="Tampilkan kata sandi"
                                                        class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button type="submit"
                                                class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-extrabold shadow-md transition inline-flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-shield-check"></i>
                                            <span>Perbarui Kata Sandi</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.dataset.target);
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye', !isPassword);
                        icon.classList.toggle('fa-eye-slash', isPassword);
                    }
                });
            });
        });
    </script>
</body>
</html>