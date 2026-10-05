<x-guest-layout>
    <!-- Container Utama: Layar Penuh dengan Background Gelap -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-[#083329]">
        
        <!-- Card Login Split Screen -->
        <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[500px]">
            
            <!-- Sisi Kiri: Banner Logo SMP Djojoredjo (Gradien Hijau) -->
            <div class="w-full md:w-1/2 bg-gradient-to-b from-[#4ade80] to-[#e2e8f0] p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                <!-- Wrapper Logo Sekolah -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-48 h-48 md:w-56 md:h-56 mb-2 flex items-center justify-center">
                        <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}" 
                             alt="Logo SMP Djojoredjo" 
                             class="w-full h-full object-contain drop-shadow-xl">
                    </div>
                    
                    <!-- Judul Sekolah -->
                    <h1 class="text-3xl font-extrabold text-[#064e3b] tracking-wider drop-shadow-sm font-serif" style="text-shadow: 2px 2px 0px #a7f3d0, -1px -1px 0px #fff;">
                        SMP
                    </h1>
                    <h1 class="text-4xl font-extrabold text-[#059669] tracking-wider drop-shadow-sm uppercase font-serif" style="text-shadow: 2px 2px 0px #a7f3d0, -1px -1px 0px #fff;">
                        DJOJOREDJO
                    </h1>
                </div>
            </div>

            <!-- Sisi Kanan: Form Input Login -->
            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white">
                
                <!-- Title Header Form -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Login Pengguna</h2>
                    <p class="text-xs text-slate-400 mt-1">Login menggunakan akun terdaftar</p>
                </div>

                <!-- Session Status Alerts -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Username / Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Username / Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-300">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="masukan id" 
                                   class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition placeholder:text-slate-300 shadow-sm">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-300">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   required 
                                   placeholder="password" 
                                   class="w-full pl-11 pr-10 py-3 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition placeholder:text-slate-300 shadow-sm">
                            
                            <!-- Tombol Toggle Visibilitas Password -->
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-500 hover:text-emerald-700 focus:outline-none">
                                <i class="fa-solid fa-eye-slash text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>

                    <!-- Lupa Password Link & Remember Me -->
                    <div class="flex items-center justify-between text-xs pt-2">
                        <label for="remember_me" class="inline-flex items-center text-slate-500 hover:text-slate-700 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 text-xs">
                            <span class="ms-2">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-slate-400 hover:text-emerald-600 transition" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Tombol Login -->
                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full py-3.5 px-4 bg-[#34d399] hover:bg-[#10b981] text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-500/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2">
                            Login
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Script Interaktif untuk Mata Password -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Ubah tipe input (password <-> text)
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Ubah ikon mata (terbuka <-> tercoret)
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</x-guest-layout>