<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Pengguna - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 untuk Konfirmasi Hapus -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased">

    <div class="flex min-h-screen relative">
        
        <!-- PANGGIL SIDEBAR -->
        @include('layouts.partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            
            <!-- TOPBAR HEADER -->
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 flex justify-between items-center gap-3 sticky top-0 z-20">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <button type="button" id="mobileSidebarBtn" aria-label="Buka Menu"
                        class="p-2 -ml-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-slate-800 truncate">Kelola Akun Pengguna</h2>
                        <p class="hidden sm:block text-xs text-slate-500 truncate">Daftar, tambah, dan hapus akun pengguna sistem</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-4 shrink-0">
                    <div class="hidden md:flex items-center space-x-2 text-xs text-slate-500 border border-slate-200 rounded-lg px-3 py-1.5 bg-slate-50">
                        <i class="fa-regular fa-calendar"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- BODY CONTENT TABLE & ACTIONS -->
            <div class="p-4 sm:p-6 lg:p-8 flex-1">
                <div class="max-w-6xl mx-auto space-y-4 sm:space-y-6">

                    <!-- Flash Message Success -->
                    @if(session('success'))
                        <div class="p-3.5 sm:p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl flex items-start sm:items-center gap-2.5">
                            <i class="fa-solid fa-circle-check mt-0.5 sm:mt-0 shrink-0"></i>
                            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Flash Message Error -->
                    @if(session('error'))
                        <div class="p-3.5 sm:p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl flex items-start sm:items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 sm:mt-0 shrink-0"></i>
                            <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Card Header & Action Buttons -->
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3.5 sm:gap-4">
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800">Daftar Pengguna</h3>
                            <p class="text-xs text-slate-500">Total pengguna terdaftar di dalam database</p>
                        </div>
                        
                        <div class="w-full sm:w-auto">
                            <a href="{{ Route::has('admin.users.create') ? route('admin.users.create') : '#' }}" 
                               class="w-full sm:w-auto justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-medium text-xs sm:text-sm rounded-lg shadow-sm transition flex items-center gap-2">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Tambah Akun Baru</span>
                            </a>
                        </div>
                    </div>

                    <!-- DATA PENGGUNA CONTAINER -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        
                        <!-- TAMPILAN HP (MOBILE CARD VIEW: < 768px) -->
                        <div class="block md:hidden divide-y divide-slate-200">
                            @forelse($users ?? [] as $index => $user)
                                <div class="p-4 space-y-3 hover:bg-slate-50/60 transition">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-semibold text-sm text-slate-800 truncate">{{ $user->name }}</p>
                                                <p class="text-xs text-slate-500 break-all">{{ $user->email }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">
                                                    No. {{ method_exists($users, 'firstItem') && $users->firstItem() ? $users->firstItem() + $index : $loop->iteration }} • ID: #{{ $user->id }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="shrink-0">
                                            @if(strtolower($user->role) === 'admin')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <i class="fa-solid fa-user-shield mr-1 text-[10px]"></i> Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                    <i class="fa-solid fa-chalkboard-user mr-1 text-[10px]"></i> Guru
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                                        @if(auth()->id() !== $user->id)
                                            <button type="button"
                                                    data-id="{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    onclick="confirmDelete(this)"
                                                    class="w-full sm:w-auto justify-center px-3 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition border border-red-200 text-xs font-medium flex items-center gap-1.5">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Hapus Akun</span>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic py-1">Akun Anda (Sedang Aktif)</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-10 px-4 text-center text-slate-400">
                                    <i class="fa-solid fa-users-slash text-3xl mb-2"></i>
                                    <p class="text-sm">Belum ada data pengguna.</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (TABLE VIEW: >= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        <th class="py-3.5 px-4 lg:px-6 w-16">No</th>
                                        <th class="py-3.5 px-4 lg:px-6">Pengguna</th>
                                        <th class="py-3.5 px-4 lg:px-6">Email</th>
                                        <th class="py-3.5 px-4 lg:px-6 whitespace-nowrap">Role / Hak Akses</th>
                                        <th class="py-3.5 px-4 lg:px-6 text-center whitespace-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm">
                                    @forelse($users ?? [] as $index => $user)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-4 px-4 lg:px-6 font-medium text-slate-500">
                                                {{ method_exists($users, 'firstItem') && $users->firstItem() ? $users->firstItem() + $index : $loop->iteration }}
                                            </td>
                                            <td class="py-4 px-4 lg:px-6">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                                        <p class="text-xs text-slate-400">ID: #{{ $user->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 lg:px-6 text-slate-600 break-all">
                                                {{ $user->email }}
                                            </td>
                                            <td class="py-4 px-4 lg:px-6 whitespace-nowrap">
                                                @if(strtolower($user->role) === 'admin')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <i class="fa-solid fa-user-shield mr-1.5 text-[10px]"></i> Admin
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                        <i class="fa-solid fa-chalkboard-user mr-1.5 text-[10px]"></i> Guru
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-4 lg:px-6 text-center whitespace-nowrap">
                                                @if(auth()->id() !== $user->id)
                                                    <form id="delete-form-{{ $user->id }}" 
                                                          action="{{ Route::has('admin.users.destroy') ? route('admin.users.destroy', $user->id) : '#' }}" 
                                                          method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        
                                                        <button type="button"
                                                                data-id="{{ $user->id }}"
                                                                data-name="{{ $user->name }}"
                                                                onclick="confirmDelete(this)"
                                                                class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition border border-red-200"
                                                                title="Hapus Akun">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">Akun Anda</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400">
                                                <i class="fa-solid fa-users-slash text-3xl mb-2"></i>
                                                <p class="text-sm">Belum ada data pengguna.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Link Paginasi -->
                        @if(method_exists($users, 'hasPages') && $users->hasPages())
                            <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-t border-slate-200 bg-slate-50 overflow-x-auto">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- SCRIPT KONFIRMASI HAPUS & TOGGLE SIDEBAR -->
    <script>
        function confirmDelete(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Akun Pengguna?',
                text: `Apakah Anda yakin ingin menghapus akun "${name}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }

        // Toggle sidebar untuk layar HP & Tablet
        document.getElementById('mobileSidebarBtn')?.addEventListener('click', function () {
            const sidebar = document.querySelector('aside') || document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-y-0');
                sidebar.classList.toggle('left-0');
                sidebar.classList.toggle('z-40');
            }
        });
    </script>
</body>
</html>