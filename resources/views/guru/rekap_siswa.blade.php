<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#EAF0F6] font-sans antialiased overflow-x-hidden">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">REKAP ABSENSI SISWA</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-6">
                
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-slate-100">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-100 pb-4 mb-6 gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 uppercase">REKAPITULASI PRESENSI SISWA</h2>
                            <p class="text-xs text-slate-500">Hasil rekap kehadiran siswa berdasarkan kelas yang Anda ajar.</p>
                        </div>
                        <a href="{{ route('guru.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition flex items-center gap-2">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('guru.rekap-siswa') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Kelas</label>
                            <select name="kelas" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                                <option value="">Semua Kelas</option>
                                @foreach($kelases as $k)
                                    <option value="{{ $k->nama_kelas }}" {{ strtolower($selectedKelas) == strtolower($k->nama_kelas) ? 'selected' : '' }}>
                                        Kelas {{ $k->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Bulan</label>
                            <select name="bulan" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $selectedBulan == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Tahun</label>
                            <select name="tahun" onchange="this.form.submit()" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                                @for($y=date('Y'); $y>=date('Y')-2; $y--)
                                    <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>

                    <!-- Table Rekap -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                <tr>
                                    <th class="p-3">NO</th>
                                    <th class="p-3">NAMA SISWA</th>
                                    <th class="p-3">KELAS</th>
                                    <th class="p-3 text-center">HADIR</th>
                                    <th class="p-3 text-center">SAKIT</th>
                                    <th class="p-3 text-center">IZIN</th>
                                    <th class="p-3 text-center">ALPA</th>
                                    <th class="p-3 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($rekapData as $index => $data)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-semibold">{{ $index + 1 }}</td>
                                        <td class="p-3 font-bold text-slate-800">{{ $data->nama }}</td>
                                        <td class="p-3">Kelas {{ $data->kelas }}</td>
                                        <td class="p-3 text-center"><span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">{{ $data->hadir }}</span></td>
                                        <td class="p-3 text-center"><span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full font-bold text-[10px]">{{ $data->sakit }}</span></td>
                                        <td class="p-3 text-center"><span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[10px]">{{ $data->izin }}</span></td>
                                        <td class="p-3 text-center"><span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full font-bold text-[10px]">{{ $data->alpa }}</span></td>
                                        <td class="p-3 text-center">
                                            <form id="form-hapus-{{ $data->id }}"
                                                  action="{{ route('guru.rekap-siswa.hapus', $data->id) }}"
                                                  method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="kelas" value="{{ $selectedKelas }}">
                                                <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                                                <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                                                <button type="button"
                                                        data-id="{{ $data->id }}"
                                                        data-nama="{{ $data->nama }}"
                                                        onclick="confirmHapus(this.dataset.id, this.dataset.nama)"
                                                        class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-[10px] font-bold transition inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-6 text-center text-slate-400 italic">Data rekap presensi belum tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- POP-UP SWEETALERT2 - SUKSES SIMPAN SESI -->
    @if(session('swal_success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: "{{ session('swal_title') }}",
                html: `<div class="text-xs text-slate-600 leading-relaxed mt-2">
                        <p class="mb-3">{{ session('swal_message') }}</p>
                        <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-2xl text-left space-y-1.5 shadow-inner">
                            <p class="flex justify-between"><span><strong>Kelas:</strong></span> <span class="font-bold text-slate-800">Kelas {{ session('swal_kelas') }}</span></p>
                            <p class="flex justify-between"><span><strong>Mata Pelajaran:</strong></span> <span class="font-bold text-slate-800">{{ session('swal_mapel') }}</span></p>
                            <p class="flex justify-between"><span><strong>Status Sesi:</strong></span> <span class="text-emerald-600 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check"></i> Terverifikasi</span></p>
                        </div>
                      </div>`,
                showDenyButton: true,
                confirmButtonText: '<i class="fa-solid fa-list-check mr-1.5"></i> Tetap Di Rekap',
                denyButtonText: '<i class="fa-solid fa-house mr-1.5"></i> Ke Dashboard',
                confirmButtonColor: '#19A389',
                denyButtonColor: '#64748b',
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                    title: 'text-base font-bold text-slate-800 uppercase tracking-wide',
                    confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold shadow-md',
                    denyButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold shadow-md'
                }
            }).then((result) => {
                if (result.isDenied) {
                    window.location.href = "{{ route('guru.dashboard') }}";
                }
            });
        });
    </script>
    @endif

    <!-- POP-UP SWEETALERT2 - NOTIFIKASI UMUM (mis. hasil hapus) -->
    @if(session('success') && !session('swal_success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK',
                confirmButtonColor: '#19A389',
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                    confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold shadow-md'
                }
            });
        });
    </script>
    @endif

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        // KONFIRMASI HAPUS REKAP PRESENSI SISWA
        function confirmHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Rekap Presensi?',
                html: `Seluruh data kehadiran <strong>${nama}</strong> untuk periode yang sedang ditampilkan akan dihapus permanen dan tidak bisa dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#CE1B1B',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold',
                    cancelButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-hapus-' + id).submit();
                }
            });
        }
    </script>
</body>
</html>