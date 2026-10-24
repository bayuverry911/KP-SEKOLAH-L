@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">KALENDER AKADEMIK</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-6 mt-2">

                @if(session('success'))
                    <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i><span>{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i><span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- ================= HARI LIBUR ================= -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-calendar-xmark text-rose-500"></i> Hari Libur / Tanggal Merah
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tanggal di sini tampil merah di Rekap Absen Siswa dan tidak dihitung sebagai hari efektif belajar. Guru tidak dapat absen pada tanggal libur.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.kalender.index') }}" class="flex items-center gap-2 text-xs">
                            <label class="font-semibold text-slate-500">Tahun</label>
                            <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2100"
                                   class="w-24 p-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <button class="px-3 py-2 bg-slate-800 text-white rounded-xl"><i class="fa-solid fa-filter"></i></button>
                        </form>
                    </div>

                    <form action="{{ route('admin.kalender.libur.store') }}" method="POST"
                          class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Keterangan</label>
                            <input type="text" name="keterangan" required maxlength="100" placeholder="Contoh: Hari Raya Natal"
                                   class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-slate-600 mb-1">Jenis</label>
                            <select name="jenis" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                                <option value="nasional">Nasional</option>
                                <option value="sekolah">Sekolah</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl transition">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Libur
                            </button>
                        </div>
                    </form>

                    <form action="{{ route('admin.kalender.libur.tetap') }}" method="POST" class="text-xs flex flex-wrap items-center gap-2">
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $tahun }}">
                        <button type="submit" class="px-3 py-2 bg-teal-50 text-[#138A73] font-semibold rounded-xl hover:bg-teal-100 transition">
                            <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Isi libur tanggal tetap {{ $tahun }}
                        </button>
                        <span class="text-slate-400">(1 Jan, 1 Mei, 1 Jun, 17 Agu, 25 Des). Libur yang tanggalnya berubah tiap tahun input manual sesuai SKB 3 Menteri.</span>
                    </form>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                <tr>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Hari</th>
                                    <th class="p-3">Keterangan</th>
                                    <th class="p-3">Jenis</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($liburs as $l)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-semibold text-slate-800">{{ $l->tanggal->format('d/m/Y') }}</td>
                                        <td class="p-3">{{ Absen::namaHari($l->tanggal) }}</td>
                                        <td class="p-3">{{ $l->keterangan }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $l->jenis === 'nasional' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ ucfirst($l->jenis) }}</span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <form action="{{ route('admin.kalender.libur.destroy', $l->id) }}" method="POST"
                                                  onsubmit="return confirm('Hapus hari libur ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-rose-600 hover:text-rose-800 font-semibold"><i class="fa-solid fa-trash text-xs"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-4 text-center text-slate-400 italic">Belum ada hari libur untuk tahun {{ $tahun }}.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ================= PERIODE PEMBELAJARAN ================= -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-[#19A389]"></i> Periode Pembelajaran
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Muncul sebagai pilihan "Rentang Pembelajaran" di Rekap Absen Siswa (contoh: Pertengahan Oktober s/d UAS).</p>
                    </div>

                    <form action="{{ route('admin.kalender.periode.store') }}" method="POST"
                          class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                        @csrf
                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Nama Periode</label>
                            <input type="text" name="nama" required maxlength="100" placeholder="Contoh: Pertengahan Oktober s/d UAS"
                                   class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Selesai</label>
                            <input type="date" name="tanggal_selesai" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl transition">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                <tr>
                                    <th class="p-3">Nama Periode</th>
                                    <th class="p-3">Mulai</th>
                                    <th class="p-3">Selesai</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($periodes as $p)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-semibold text-slate-800">{{ $p->nama }}</td>
                                        <td class="p-3">{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                                        <td class="p-3">{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                        <td class="p-3 text-center">
                                            <form action="{{ route('admin.kalender.periode.destroy', $p->id) }}" method="POST"
                                                  onsubmit="return confirm('Hapus periode ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-rose-600 hover:text-rose-800 font-semibold"><i class="fa-solid fa-trash text-xs"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-slate-400 italic">Belum ada periode pembelajaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
