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
                        <p class="text-xs text-slate-500">Daftar, tambah, dan hapus akun pengguna sistem</p>
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

            <!-- BODY CONTENT TABLE & ACTIONS -->
            <div class="p-8">
                <div class="max-w-6xl mx-auto space-y-6">

                    <!-- Flash Message Success -->
                    @if(session('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Flash Message Error -->
                    @if(session('error'))
                        <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Card Header & Action Buttons -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Daftar Pengguna</h3>
                            <p class="text-xs text-slate-500">Total pengguna terdaftar di dalam database</p>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <a href="{{ Route::has('admin.users.create') ? route('admin.users.create') : '#' }}" 
                               class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-lg shadow-sm transition flex items-center space-x-2">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Tambah Akun Baru</span>
                            </a>
                        </div>
                    </div>

                    <!-- Tabel Data Pengguna -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        <th class="py-3.5 px-6">No</th>
                                        <th class="py-3.5 px-6">Pengguna</th>
                                        <th class="py-3.5 px-6">Email</th>
                                        <th class="py-3.5 px-6">Role / Hak Akses</th>
                                        <th class="py-3.5 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 text-sm">
                                    @forelse($users ?? [] as $index =>$user)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-4 px-6 font-medium text-slate-500">
                                                {{ $users->firstItem() ?$users->firstItem() + $index :$loop->iteration }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                                        <p class="text-xs text-slate-400">ID: #{{ $user->id }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-slate-600">
                                                {{ $user->email }}
                                            </td>
                                            <td class="py-4 px-6">
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
                                            <td class="py-4 px-6 text-center">
                                                @if(auth()->id() !== $user->id)
                                                    <!-- Form Hapus Akun -->
                                                    <form id="delete-form-{{ $user->id }}" 
                                                          action="{{ Route::has('admin.users.destroy') ? route('admin.users.destroy', $user->id) : '#' }}" 
                                                          method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        
                                                        <button type="button" onclick="confirmDelete('{{ $user->id }}', '{{$user->name }}')"
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
                        @if(method_exists($users, 'hasPages') &&$users->hasPages())
                            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- SCRIPT KONFIRMASI HAPUS -->
    <script>
        function confirmDelete(id, name) {
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
    </script>
</body>
</html>