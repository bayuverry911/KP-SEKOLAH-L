@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - SMP Djojoredjo</title>
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

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <!-- TOPBAR HEADER -->
            <header class="bg-[#EAF0F6]/90 backdrop-blur-md sticky top-0 z-20 px-4 sm:px-8 py-3.5 flex justify-between items-center gap-3 border-b border-slate-200/50 lg:border-none">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="mobileSidebarBtn" aria-label="Buka Menu"
                        class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl bg-white shadow-sm border border-slate-200/60 transition shrink-0">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest truncate">
                        MASTER DATA • KALENDER AKADEMIK
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($tahun)
                        <span class="hidden sm:inline-flex items-center px-3 py-1.5 bg-white rounded-xl shadow-sm border border-slate-200/60 text-xs font-semibold text-slate-600">
                            <i class="fa-solid fa-graduation-cap text-[#19A389] mr-1.5"></i> TA: <b class="ml-1">{{ $tahun->nama }}</b>
                        </span>
                    @endif
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
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Pengaturan Waktu & Hari Efektif</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Kalender Akademik & Hari Libur</h2>
                            <p class="text-xs text-white/80 mt-1">Kelola tahun pelajaran aktif, penandaan hari libur nasional/sekolah, serta periode pembelajaran</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>{{ $tahun ? 'TA ' . $tahun->nama : $tahunList->count() . ' Tahun Pelajaran' }}</span>
                            </div>
                            @if($tahun)
                                <div class="bg-rose-500/25 backdrop-blur-sm border border-rose-200/30 px-3.5 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-1.5 text-rose-100">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                    <span>{{ $liburs->count() }} Hari Libur</span>
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

                    <!-- ================= TAHUN PELAJARAN ================= -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">
                        <div class="flex items-start gap-3 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0 mt-0.5">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Tahun Pelajaran</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                                    Tahun pelajaran <b>aktif</b> menjadi default filter tanggal di akun guru (Rekap Absen Siswa). Hari libur yang Anda tandai otomatis tidak dihitung sebagai hari efektif.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('admin.kalender.tahun.store') }}" method="POST"
                              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                            @csrf
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Tahun Pelajaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama" required maxlength="30" placeholder="Contoh: 2026/2027" value="{{ old('nama') }}"
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Tanggal Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}"
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Tanggal Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}"
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="sm:col-span-2 lg:col-span-3">
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah Tahun Pelajaran</span>
                                </button>
                            </div>
                        </form>

                        <div class="flex flex-wrap gap-2.5">
                            @forelse($tahunList as $t)
                                <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border {{ $tahun && $tahun->id === $t->id ? 'border-[#19A389] bg-emerald-50/70 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }} pl-3.5 pr-1.5 py-1.5 text-xs transition">
                                    <a href="{{ route('admin.kalender.index', ['tahun' => $t->id]) }}" class="font-bold text-slate-800 hover:text-[#138A73] transition">
                                        {{ $t->nama }}
                                        <span class="font-normal text-slate-500 ml-0.5">({{ $t->tanggal_mulai->format('d/m/Y') }} - {{ $t->tanggal_selesai->format('d/m/Y') }})</span>
                                    </a>
                                    @if($t->aktif)
                                        <span class="ml-1 px-2.5 py-0.5 rounded-full bg-[#19A389] text-white text-[10px] font-extrabold tracking-wide">AKTIF</span>
                                    @else
                                        <form action="{{ route('admin.kalender.tahun.aktif', $t->id) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="ml-1 px-2.5 py-0.5 rounded-full bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-[#0D6353] text-[10px] font-bold transition">
                                                Jadikan Aktif
                                            </button>
                                        </form>
                                    @endif
                                    <form id="delete-tahun-{{ $t->id }}" action="{{ route('admin.kalender.tahun.destroy', $t->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                data-id="{{ $t->id }}"
                                                data-name="{{ $t->nama }}"
                                                onclick="confirmDeleteTahun(this)"
                                                class="w-7 h-7 rounded-xl text-rose-500 hover:bg-rose-100 hover:text-rose-700 inline-flex items-center justify-center transition"
                                                title="Hapus Tahun Pelajaran">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="w-full p-6 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-400 italic">
                                    <i class="fa-regular fa-calendar-plus text-xl block mb-1.5 text-slate-300"></i>
                                    Belum ada Tahun Pelajaran. Tambahkan dulu (mis. 2026/2027: 13/07/2026 - 30/06/2027).
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- ================= KALENDER PER BULAN ================= -->
                    @if($tahun)
                        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-100">
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-base shrink-0 mt-0.5">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                                            Hari Libur Tahun Pelajaran {{ $tahun->nama }}
                                        </h3>
                                        <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                                            Klik tanggal untuk menandai, melihat, atau menghapus hari libur. Total <b>{{ $liburs->count() }}</b> hari libur tercatat.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2.5">
                                    <!-- Legenda Warna -->
                                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-50 border border-slate-200/70 rounded-xl text-[11px] text-slate-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-sm bg-rose-500 inline-block"></span> Libur
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-sm bg-slate-200 inline-block"></span> Akhir Pekan
                                        </span>
                                    </div>

                                    <form action="{{ route('admin.kalender.libur.tetap') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="tahun_id" value="{{ $tahun->id }}">
                                        <button type="submit"
                                                class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-[#0D6353] border border-emerald-200 text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5">
                                            <i class="fa-solid fa-wand-magic-sparkles text-[#19A389]"></i>
                                            <span>Isi Libur Tanggal Tetap</span>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
                                @foreach($blok as $b)
                                    <div class="border border-slate-200/90 rounded-2xl overflow-hidden bg-white shadow-sm flex flex-col">
                                        <div class="bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white text-xs font-bold px-3.5 py-2.5 flex items-center justify-between">
                                            <span>{{ $b['judul'] }}</span>
                                            @if(count($b['libur']))
                                                <span class="px-2 py-0.5 rounded-full bg-rose-500/90 text-white text-[10px] font-extrabold">
                                                    {{ count($b['libur']) }} libur
                                                </span>
                                            @endif
                                        </div>

                                        <div class="p-2">
                                            <table class="w-full text-center text-[11px] border-separate border-spacing-0">
                                                <thead>
                                                    <tr class="text-slate-500">
                                                        @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)
                                                            <th class="py-1.5 font-bold border-b border-slate-100 {{ in_array($h, ['Sab','Min']) ? 'text-rose-500' : '' }}">{{ $h }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($b['minggu'] as $minggu)
                                                        <tr>
                                                            @foreach($minggu as $c)
                                                                @if($c === null)
                                                                    <td class="p-0"></td>
                                                                @else
                                                                    <td class="p-0.5">
                                                                        @php
                                                                            $kelasSel = $c['luar'] ? 'text-slate-300 cursor-not-allowed'
                                                                                : ($c['libur'] ? 'bg-rose-500 text-white font-bold hover:bg-rose-600 shadow-xs'
                                                                                : ($c['weekend'] ? 'bg-slate-50 text-rose-500 font-medium hover:bg-slate-100' : 'text-slate-700 font-medium hover:bg-emerald-50 hover:text-[#0D6353]'));
                                                                        @endphp
                                                                        <button type="button"
                                                                                class="btn-tgl w-full h-8 sm:h-7 rounded-lg transition {{ $kelasSel }}"
                                                                                {{ $c['luar'] ? 'disabled' : '' }}
                                                                                data-tgl="{{ $c['tgl'] }}"
                                                                                data-tampil="{{ \Carbon\Carbon::parse($c['tgl'])->format('d/m/Y') }}"
                                                                                data-hari="{{ Absen::namaHari(\Carbon\Carbon::parse($c['tgl'])) }}"
                                                                                data-libur="{{ $c['libur'] }}"
                                                                                data-jenis="{{ $c['jenis'] }}"
                                                                                data-id="{{ $c['id'] }}"
                                                                                title="{{ $c['libur'] }}">{{ $c['hari'] }}</button>
                                                                    </td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @if(count($b['libur']))
                                            <ul class="px-3.5 py-2.5 text-[11px] text-slate-600 bg-rose-50/40 border-t border-slate-100 space-y-1 mt-auto">
                                                @foreach($b['libur'] as $l)
                                                    <li class="flex items-start gap-1.5">
                                                        <span class="font-extrabold text-rose-600 shrink-0">{{ $l['hari'] }}</span>
                                                        <span class="text-slate-400">–</span>
                                                        <span class="leading-snug">{{ $l['keterangan'] }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- ================= PERIODE PEMBELAJARAN ================= -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">
                        <div class="flex items-start gap-3 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0 mt-0.5">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900">Periode Pembelajaran</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                                    Muncul sebagai pilihan "Rentang Pembelajaran" di Rekap Absen Siswa. Periode yang mencakup hari ini otomatis menjadi tampilan awal guru.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('admin.kalender.periode.store') }}" method="POST"
                              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                            @csrf
                            <div class="sm:col-span-2 lg:col-span-4">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Nama Periode <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama" required maxlength="100" placeholder="Contoh: Pertengahan Oktober s/d UAS"
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_mulai" required
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" name="tanggal_selesai" required
                                       class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] transition">
                            </div>
                            <div class="sm:col-span-2 lg:col-span-2">
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Tambah</span>
                                </button>
                            </div>
                        </form>

                        <!-- TAMPILAN HP PERIODE (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100 border border-slate-200 rounded-2xl overflow-hidden">
                            @forelse($periodes as $p)
                                <div class="p-4 space-y-2.5 bg-white">
                                    <p class="font-bold text-sm text-slate-800">{{ $p->nama }}</p>
                                    <div class="flex items-center justify-between bg-slate-50 px-3 py-2 rounded-xl text-xs text-slate-600">
                                        <span>Mulai: <b class="text-slate-800">{{ $p->tanggal_mulai->format('d/m/Y') }}</b></span>
                                        <span>Selesai: <b class="text-slate-800">{{ $p->tanggal_selesai->format('d/m/Y') }}</b></span>
                                    </div>
                                    <form id="delete-periode-{{ $p->id }}" action="{{ route('admin.kalender.periode.destroy', $p->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                data-id="{{ $p->id }}"
                                                data-name="{{ $p->nama }}"
                                                onclick="confirmDeletePeriode(this)"
                                                class="w-full py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold inline-flex items-center justify-center gap-1.5 transition">
                                            <i class="fa-solid fa-trash-can"></i> Hapus Periode
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 italic text-xs bg-white">
                                    Belum ada periode pembelajaran.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP PERIODE (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-3.5 w-12 text-center">No</th>
                                        <th class="p-3.5">Nama Periode</th>
                                        <th class="p-3.5 whitespace-nowrap">Mulai</th>
                                        <th class="p-3.5 whitespace-nowrap">Selesai</th>
                                        <th class="p-3.5 text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($periodes as $p)
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="p-3.5 text-center font-semibold text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="p-3.5 font-bold text-slate-800">{{ $p->nama }}</td>
                                            <td class="p-3.5 whitespace-nowrap">{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                                            <td class="p-3.5 whitespace-nowrap">{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                            <td class="p-3.5 text-center">
                                                <form id="delete-periode-desktop-{{ $p->id }}" action="{{ route('admin.kalender.periode.destroy', $p->id) }}" method="POST" class="inline-block">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                            data-id="{{ $p->id }}"
                                                            data-name="{{ $p->nama }}"
                                                            onclick="confirmDeletePeriode(this, true)"
                                                            title="Hapus Periode"
                                                            class="w-8 h-8 bg-rose-100 text-rose-600 hover:bg-rose-200 rounded-xl inline-flex items-center justify-center transition">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-6 text-center text-slate-400 italic">
                                                Belum ada periode pembelajaran.
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

    <!-- MODAL TANDAI HARI LIBUR -->
    <div id="modalLibur" data-url-hapus="{{ route('admin.kalender.libur.destroy', ['libur' => 0]) }}" class="hidden fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-sm border border-slate-100">
            <div class="flex justify-between items-start gap-3 mb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                    <div>
                        <h3 id="liburJudul" class="text-base font-extrabold text-slate-900">Tandai Hari Libur</h3>
                        <p id="liburTanggal" class="text-xs font-semibold text-[#19A389]"></p>
                    </div>
                </div>
                <button type="button" id="btnTutupLibur" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.kalender.libur.store') }}" method="POST" id="formLiburBaru" class="space-y-3.5 pt-2 border-t border-slate-100">
                @csrf
                <input type="hidden" name="tanggal" id="liburInputTanggal">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Keterangan <span class="text-rose-500">*</span></label>
                    <input type="text" name="keterangan" id="liburKeterangan" required maxlength="100" placeholder="Contoh: Hari Raya Idul Fitri"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Jenis Libur</label>
                    <select name="jenis" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                        <option value="nasional">Libur Nasional / Cuti Bersama</option>
                        <option value="sekolah">Libur Sekolah</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-extrabold rounded-xl shadow transition">
                    <i class="fa-solid fa-calendar-xmark mr-1"></i> Tandai Libur
                </button>
            </form>

            <form method="POST" id="formLiburHapus" class="hidden space-y-3.5 pt-2 border-t border-slate-100">
                @csrf @method('DELETE')
                <div id="liburAda" class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 text-xs font-bold"></div>
                <button type="button" onclick="confirmDeleteLibur()" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-extrabold rounded-xl shadow transition">
                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus Hari Libur
                </button>
            </form>
        </div>
    </div>

    <script>
        const modalLibur = document.getElementById('modalLibur');
        const URL_HAPUS_LIBUR = modalLibur.dataset.urlHapus;
        const formBaru = document.getElementById('formLiburBaru');
        const formHapus = document.getElementById('formLiburHapus');

        document.querySelectorAll('.btn-tgl').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('liburTanggal').textContent = btn.dataset.hari + ', ' + btn.dataset.tampil;

                if (btn.dataset.id) {
                    document.getElementById('liburJudul').textContent = 'Detail Hari Libur';
                    document.getElementById('liburAda').textContent = btn.dataset.libur + ' (' + btn.dataset.jenis + ')';
                    formHapus.action = URL_HAPUS_LIBUR.replace(/\/0$/, '/' + btn.dataset.id);
                    formBaru.classList.add('hidden');
                    formHapus.classList.remove('hidden');
                } else {
                    document.getElementById('liburJudul').textContent = 'Tandai Hari Libur';
                    document.getElementById('liburInputTanggal').value = btn.dataset.tgl;
                    document.getElementById('liburKeterangan').value = '';
                    formHapus.classList.add('hidden');
                    formBaru.classList.remove('hidden');
                }

                modalLibur.classList.remove('hidden');
            });
        });

        const closeModalLibur = () => modalLibur.classList.add('hidden');
        document.getElementById('btnTutupLibur').addEventListener('click', closeModalLibur);
        modalLibur.addEventListener('click', function (e) { if (e.target === modalLibur) closeModalLibur(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModalLibur(); });

        function confirmDeleteTahun(btn) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');

            Swal.fire({
                title: 'Hapus Tahun Pelajaran?',
                text: `Hapus tahun pelajaran "${name}"? Data hari libur tetap tersimpan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-tahun-${id}`).submit();
                }
            });
        }

        function confirmDeletePeriode(btn, isDesktop = false) {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const formId = isDesktop ? `delete-periode-desktop-${id}` : `delete-periode-${id}`;

            Swal.fire({
                title: 'Hapus Periode Pembelajaran?',
                text: `Apakah Anda yakin ingin menghapus periode "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function confirmDeleteLibur() {
            const info = document.getElementById('liburAda').textContent;

            Swal.fire({
                title: 'Hapus Hari Libur?',
                text: `Hapus penandaan hari libur "${info}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    formHapus.submit();
                }
            });
        }
    </script>
</body>
</html>