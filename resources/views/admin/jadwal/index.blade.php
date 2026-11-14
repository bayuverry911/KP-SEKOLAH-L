<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Mengajar - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">

            <!-- TOPBAR HEADER -->
            <header class="bg-[#EAF0F6]/90 backdrop-blur-md sticky top-0 z-20 px-4 sm:px-8 py-3.5 flex justify-between items-center gap-3 border-b border-slate-200/50 lg:border-none">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="mobileSidebarBtn" aria-label="Buka Menu"
                        class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl bg-white shadow-sm border border-slate-200/60 transition shrink-0">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest truncate">
                        MASTER DATA • JADWAL MENGAJAR
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="hidden sm:inline-flex items-center px-3 py-1.5 bg-white rounded-xl shadow-sm border border-slate-200/60 text-xs font-semibold text-slate-600">
                        <i class="fa-solid fa-calendar-days text-[#19A389] mr-1.5"></i> Total: <b class="ml-1">{{ $jadwals->count() }}</b>
                    </span>
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}"
                       class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition">
                        <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Penjadwalan Akademik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Kelola Jadwal Mengajar Guru</h2>
                            <p class="text-xs text-white/80 mt-1">Atur jam pelajaran guru per kelas dan penugasan guru pengganti di SMP Djojoredjo</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2">
                                <i class="fa-solid fa-clock"></i>
                                <span>{{ $jadwals->count() }} Jadwal Aktif</span>
                            </div>
                            @if($penggantis->count() > 0)
                                <div class="bg-amber-400/20 backdrop-blur-sm border border-amber-200/30 px-3.5 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-1.5 text-amber-100">
                                    <i class="fa-solid fa-user-clock"></i>
                                    <span>{{ $penggantis->count() }} Pengganti</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- CARD FORM INPUT JADWAL BARU -->
                    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xl border border-slate-100">
                        <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-calendar-plus"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Tambah Jadwal Mengajar Baru</h3>
                                <p class="text-[11px] text-slate-400">Pilih guru, kelas, mata pelajaran, hari, serta jam masuk & selesai</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.jadwal.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Guru <span class="text-rose-500">*</span>
                                </label>
                                <select name="guru_id" required
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama ?? $g->nama_guru }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Kelas <span class="text-rose-500">*</span>
                                </label>
                                <select name="kelas" required
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelases as $k)
                                        <option value="{{ $k->nama_kelas }}" {{ old('kelas') == $k->nama_kelas ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Mata Pelajaran <span class="text-rose-500">*</span>
                                </label>
                                <select name="mapel" required
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach($mapels as $m)
                                        <option value="{{ $m->nama_mapel }}" {{ old('mapel') == $m->nama_mapel ? 'selected' : '' }}>
                                            {{ $m->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Hari <span class="text-rose-500">*</span>
                                </label>
                                <select name="hari" required
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hariOpt)
                                        <option value="{{ $hariOpt }}" {{ old('hari') == $hariOpt ? 'selected' : '' }}>{{ $hariOpt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Jam Masuk <span class="text-rose-500">*</span>
                                </label>
                                <input type="time" name="jam_masuk" value="{{ old('jam_masuk', '07:00') }}" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Jam Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', '08:30') }}" required
                                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div class="sm:col-span-2 lg:col-span-3 pt-2 flex justify-end">
                                <button type="submit"
                                    class="w-full sm:w-auto px-6 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Jadwal Mengajar</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- CARD LIST JADWAL MENGAJAR -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Daftar Jadwal Mengajar</h3>
                                <p class="text-[11px] text-slate-400">Filter berdasarkan hari atau cari nama guru, kelas, dan mata pelajaran</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2.5 w-full lg:w-auto">
                                <select id="filterHari" onchange="filterJadwal()"
                                    class="w-full sm:w-40 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 font-semibold focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">Semua Hari</option>
                                    <option value="senin">Senin</option>
                                    <option value="selasa">Selasa</option>
                                    <option value="rabu">Rabu</option>
                                    <option value="kamis">Kamis</option>
                                    <option value="jumat">Jumat</option>
                                    <option value="sabtu">Sabtu</option>
                                </select>
                                <div class="relative w-full sm:w-64">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" id="cariJadwal" onkeyup="filterJadwal()"
                                           placeholder="Cari guru, kelas, mapel..."
                                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                            </div>
                        </div>

                        <!-- TAMPILAN HP (MOBILE CARD VIEW: < 768px) -->
                        <div id="mobileJadwalList" class="block md:hidden divide-y divide-slate-100">
                            @forelse($jadwals as $j)
                                <div class="jadwal-item p-4 space-y-3 hover:bg-slate-50/70 transition" data-hari="{{ strtolower($j->hari) }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-slate-800 truncate">{{ $j->nama_guru }}</p>
                                            <p class="text-xs font-semibold text-[#138A73] mt-0.5">{{ $j->mapel }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-[#0D6353] border border-emerald-200 font-extrabold text-[11px] shrink-0">
                                            Kelas {{ $j->kelas }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between bg-slate-50 px-3 py-2.5 rounded-2xl text-xs">
                                        <span class="font-bold text-slate-700">
                                            <i class="fa-regular fa-calendar text-[#19A389] mr-1.5"></i>{{ $j->hari }}
                                        </span>
                                        <span class="font-mono font-bold text-slate-700">
                                            <span class="text-emerald-600">{{ substr($j->jam_masuk, 0, 5) }}</span>
                                            <span class="text-slate-400 mx-1">-</span>
                                            <span class="text-rose-600">{{ substr($j->jam_selesai, 0, 5) }}</span>
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 pt-0.5">
                                        <button type="button"
                                                class="btn-pengganti flex-1 py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition"
                                                data-url="{{ route('admin.jadwal.pengganti.store', $j->id) }}"
                                                data-info="{{ $j->kelas }} - {{ $j->mapel }} ({{ $j->hari }}, {{ substr($j->jam_masuk, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}) - {{ $j->nama_guru }}"
                                                data-hari="{{ $j->hari }}"
                                                data-guru="{{ $j->guru_id }}">
                                            <i class="fa-solid fa-user-clock"></i> Pengganti
                                        </button>
                                        <button type="button"
                                                data-id="{{ $j->id }}"
                                                data-name="{{ $j->nama_guru }} ({{ $j->kelas }} - {{ $j->mapel }})"
                                                onclick="confirmDeleteJadwal(this)"
                                                class="flex-1 py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-calendar-xmark text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada data jadwal mengajar.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (TABLE VIEW: >= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table id="tabelJadwal" class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 w-12 text-center">No</th>
                                        <th class="p-4">Guru</th>
                                        <th class="p-4">Kelas</th>
                                        <th class="p-4">Mata Pelajaran</th>
                                        <th class="p-4">Hari</th>
                                        <th class="p-4 whitespace-nowrap">Jam Masuk</th>
                                        <th class="p-4 whitespace-nowrap">Jam Selesai</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($jadwals as $j)
                                        <tr class="jadwal-item hover:bg-slate-50/90 transition" data-hari="{{ strtolower($j->hari) }}">
                                            <td class="p-4 font-semibold text-center text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="p-4 font-bold text-slate-800">{{ $j->nama_guru }}</td>
                                            <td class="p-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[#0D6353] border border-emerald-200 font-bold text-[11px]">
                                                    {{ $j->kelas }}
                                                </span>
                                            </td>
                                            <td class="p-4 font-semibold text-slate-700">{{ $j->mapel }}</td>
                                            <td class="p-4 font-semibold text-slate-700">{{ $j->hari }}</td>
                                            <td class="p-4 font-mono font-bold text-emerald-600 whitespace-nowrap">{{ $j->jam_masuk }}</td>
                                            <td class="p-4 font-mono font-bold text-rose-600 whitespace-nowrap">{{ $j->jam_selesai }}</td>
                                            <td class="p-4 text-center whitespace-nowrap">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <button type="button"
                                                            class="btn-pengganti px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-xl text-[11px] font-bold inline-flex items-center gap-1 transition"
                                                            data-url="{{ route('admin.jadwal.pengganti.store', $j->id) }}"
                                                            data-info="{{ $j->kelas }} - {{ $j->mapel }} ({{ $j->hari }}, {{ substr($j->jam_masuk, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}) - {{ $j->nama_guru }}"
                                                            data-hari="{{ $j->hari }}"
                                                            data-guru="{{ $j->guru_id }}">
                                                        <i class="fa-solid fa-user-clock"></i> Pengganti
                                                    </button>
                                                    <form id="delete-jadwal-{{ $j->id }}" action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                                data-id="{{ $j->id }}"
                                                                data-name="{{ $j->nama_guru }} ({{ $j->kelas }} - {{ $j->mapel }})"
                                                                onclick="confirmDeleteJadwal(this)"
                                                                title="Hapus Jadwal"
                                                                class="w-8 h-8 bg-rose-100 text-rose-600 hover:bg-rose-200 rounded-xl inline-flex items-center justify-center transition">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="p-8 text-center text-slate-400 italic">
                                                <i class="fa-regular fa-calendar-xmark text-2xl block mb-2 text-slate-300"></i>
                                                Belum ada data jadwal mengajar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CARD DAFTAR GURU PENGGANTI -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-base shrink-0 mt-0.5">
                                <i class="fa-solid fa-user-clock"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Guru Pengganti yang Sudah Di-plot</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Jadwal tampil otomatis di dashboard guru pengganti pada tanggal yang dipilih. Guru asli tidak dapat mengabsen jadwal tersebut pada tanggal itu.
                                </p>
                            </div>
                        </div>

                        <!-- TAMPILAN HP PENGGANTI (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($penggantis as $g)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-1 rounded-xl bg-amber-100 text-amber-800 font-extrabold text-[11px]">
                                            <i class="fa-regular fa-calendar mr-1"></i>{{ $g->tanggal->format('d/m/Y') }}
                                        </span>
                                        <span class="font-mono text-xs font-bold text-slate-600">
                                            {{ $g->jadwal ? substr($g->jadwal->jam_masuk, 0, 5) . ' - ' . substr($g->jadwal->jam_selesai, 0, 5) : '-' }}
                                        </span>
                                    </div>

                                    <div class="bg-slate-50 p-3 rounded-2xl space-y-1.5 text-xs">
                                        <p class="font-bold text-slate-800">
                                            {{ $g->jadwal->kelas ?? '-' }} • {{ $g->jadwal->mapel ?? '-' }}
                                        </p>
                                        <div class="flex flex-col gap-1 text-[11px]">
                                            <span class="text-slate-500">Guru Asli: <b class="text-slate-700">{{ $g->jadwal->nama_guru ?? '-' }}</b></span>
                                            <span class="text-amber-700">Pengganti: <b>{{ $g->nama_guru }}</b></span>
                                            @if($g->keterangan)
                                                <span class="text-slate-500 italic">Ket: "{{ $g->keterangan }}"</span>
                                            @endif
                                        </div>
                                    </div>

                                    <form id="delete-pengganti-{{ $g->id }}" action="{{ route('admin.jadwal.pengganti.destroy', $g->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-id="{{ $g->id }}"
                                                data-name="{{ $g->nama_guru }} ({{ $g->tanggal->format('d/m/Y') }})"
                                                onclick="confirmCancelPengganti(this)"
                                                class="w-full py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-xmark"></i> Batalkan Penugasan
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    Belum ada guru pengganti yang di-plot.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP PENGGANTI (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-amber-500 text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 whitespace-nowrap">Tanggal</th>
                                        <th class="p-4">Kelas / Mapel</th>
                                        <th class="p-4 whitespace-nowrap">Jam</th>
                                        <th class="p-4">Guru Asli</th>
                                        <th class="p-4">Guru Pengganti</th>
                                        <th class="p-4">Keterangan</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($penggantis as $g)
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="p-4 font-bold text-slate-800 whitespace-nowrap">{{ $g->tanggal->format('d/m/Y') }}</td>
                                            <td class="p-4 font-semibold text-slate-700">{{ $g->jadwal->kelas ?? '-' }} - {{ $g->jadwal->mapel ?? '-' }}</td>
                                            <td class="p-4 font-mono whitespace-nowrap">{{ $g->jadwal ? substr($g->jadwal->jam_masuk, 0, 5) . ' - ' . substr($g->jadwal->jam_selesai, 0, 5) : '-' }}</td>
                                            <td class="p-4">{{ $g->jadwal->nama_guru ?? '-' }}</td>
                                            <td class="p-4 font-bold text-amber-700">{{ $g->nama_guru }}</td>
                                            <td class="p-4 text-slate-500">{{ $g->keterangan ?: '-' }}</td>
                                            <td class="p-4 text-center whitespace-nowrap">
                                                <form id="delete-pengganti-desktop-{{ $g->id }}" action="{{ route('admin.jadwal.pengganti.destroy', $g->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                            data-id="{{ $g->id }}"
                                                            data-name="{{ $g->nama_guru }} ({{ $g->tanggal->format('d/m/Y') }})"
                                                            onclick="confirmCancelPengganti(this, true)"
                                                            class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-600 rounded-xl text-[11px] font-bold inline-flex items-center gap-1 transition">
                                                        <i class="fa-solid fa-xmark"></i> Batalkan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-slate-400 italic">
                                                Belum ada guru pengganti yang di-plot.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- MODAL PLOT GURU PENGGANTI -->
    <div id="modalPengganti" class="hidden fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-2xl w-full max-w-md border border-slate-100">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-clock"></i>
                    </div>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Plot Guru Pengganti</h3>
                </div>
                <button type="button" id="btnCloseX" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p id="infoPengganti" class="text-xs text-slate-600 bg-slate-50 border border-slate-200/70 rounded-xl p-2.5 mb-4 font-medium"></p>

            <form id="formPengganti" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">
                        Tanggal <span id="hintHari" class="font-semibold text-[#19A389]"></span>
                    </label>
                    <input type="date" name="tanggal" id="tanggalPengganti" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Guru Pengganti</label>
                    <select name="guru_id" id="guruPengganti" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $g)
                            <option value="{{ $g->id }}">{{ $g->nama ?? $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Keterangan (opsional)</label>
                    <input type="text" name="keterangan" maxlength="255" placeholder="Contoh: Guru asli sakit"
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                </div>
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" id="btnBatalPengganti"
                        class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="w-full sm:w-auto px-5 py-2.5 bg-[#19A389] text-white rounded-xl text-xs font-extrabold hover:bg-[#138A73] shadow transition">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ===== FILTER JADWAL (HP & DESKTOP) =====
        function filterJadwal() {
            const keyword = document.getElementById('cariJadwal').value.toLowerCase();
            const hariFilter = document.getElementById('filterHari').value.toLowerCase();

            document.querySelectorAll('.jadwal-item').forEach(el => {
                const matchText = el.textContent.toLowerCase().includes(keyword);
                const matchHari = !hariFilter || el.getAttribute('data-hari') === hariFilter;
                el.style.display = (matchText && matchHari) ? '' : 'none';
            });
        }

        // ===== KONFIRMASI HAPUS JADWAL =====
        function confirmDeleteJadwal(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Jadwal Mengajar?',
                text: `Apakah Anda yakin ingin menghapus jadwal ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-jadwal-${id}`).submit();
                }
            });
        }

        // ===== KONFIRMASI BATALKAN PENGGANTI =====
        function confirmCancelPengganti(btn, isDesktop = false) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const formId = isDesktop ? `delete-pengganti-desktop-${id}` : `delete-pengganti-${id}`;

            Swal.fire({
                title: 'Batalkan Guru Pengganti?',
                text: `Batalkan penugasan guru pengganti ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // ===== MODAL GURU PENGGANTI =====
        (function () {
            const modal = document.getElementById('modalPengganti');
            const form = document.getElementById('formPengganti');
            const tgl = document.getElementById('tanggalPengganti');
            const guruSel = document.getElementById('guruPengganti');
            const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            let hariJadwal = '';

            const today = new Date();
            tgl.min = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

            document.querySelectorAll('.btn-pengganti').forEach(btn => {
                btn.addEventListener('click', () => {
                    form.action = btn.dataset.url;
                    hariJadwal = btn.dataset.hari;
                    document.getElementById('infoPengganti').textContent = btn.dataset.info;
                    document.getElementById('hintHari').textContent = '(harus hari ' + hariJadwal + ')';
                    tgl.value = '';
                    Array.from(guruSel.options).forEach(o => {
                        o.hidden = (o.value !== '' && o.value === btn.dataset.guru);
                    });
                    guruSel.value = '';
                    modal.classList.remove('hidden');
                });
            });

            tgl.addEventListener('change', () => {
                if (!tgl.value) return;
                const nama = hari[new Date(tgl.value + 'T00:00:00').getDay()];
                tgl.setCustomValidity(nama === hariJadwal ? '' : 'Tanggal ini hari ' + nama + ', jadwal hari ' + hariJadwal + '.');
                tgl.reportValidity();
            });

            const closeModal = () => modal.classList.add('hidden');
            document.getElementById('btnBatalPengganti').addEventListener('click', closeModal);
            document.getElementById('btnCloseX').addEventListener('click', closeModal);
            modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
        })();
    </script>
</body>
</html>