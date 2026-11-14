@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Siswa - SMP Djojoredjo</title>
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
@php
    $fmtPersen = fn ($p) => $p === null ? '-' : rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . '%';
    $adaFilter = $terpilih !== null;

    $bulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $hurufHari   = ['Sn','Sl','Rb','Km','Jm','Sb','Mg'];

    $gayaHuruf = [
        'H' => 'bg-[#D1F2D9] text-[#137A43] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'I' => 'bg-[#D0EBFD] text-[#1D6F8A] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'S' => 'bg-[#FEF3D6] text-[#A16207] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'A' => 'bg-[#FDE2E2] text-[#991B1B] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
    ];

    $kolom      = $kolom ?? collect();
    $bulanGrup  = $kolom->groupBy(fn ($k) => $k->carbon->format('Y-m'));
    $kolomTetap = $semua ? 4 : 3;

    // Filter Mode & Values
    $modeAktif   = request('mode', $mode ?? 'bulan');
    $valBulan    = request('bulan', $bulan ?? \Carbon\Carbon::now()->format('Y-m'));
    $valTgl      = request('tgl', $tanggalPilih ?? \Carbon\Carbon::now()->format('Y-m-d'));
    $valPeriode  = request('periode', $periodeId ?? '');
    $valDari     = request('dari', $dari ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    $valSampai   = request('sampai', $sampai ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));
    $listPeriode = $periodeList ?? [];

    $detailKolom = $semua ? [] : $kolom->mapWithKeys(fn ($k) => [$k->tanggal => [
        'tampil'     => Absen::namaHari($k->carbon) . ', ' . $k->carbon->format('d/m/Y'),
        'tglPendek'  => $k->carbon->format('d/m/Y'),
        'state'      => $k->state,
        'keterangan' => $k->keterangan,
        'urlSusulan' => ($k->susulan && $k->susulan['boleh'])
            ? route('guru.absen-siswa.susulan', [$k->susulan['jadwal_id'], $k->tanggal, 'kelas_mapel' => $terpilih['key'], 'periode' => $valPeriode, 'dari' => $valDari, 'sampai' => $valSampai, 'mode' => $modeAktif, 'bulan' => $valBulan, 'tgl' => $valTgl])
            : null,
        'penanggung' => $k->susulan ? ['nama' => $k->susulan['nama'], 'pengganti' => $k->susulan['pengganti']] : null,
    ]])->all();
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
                        AKADEMIK • REKAP ABSENSI SISWA
                    </h1>
                </div>

                <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60 shrink-0">
                    <i class="fa-regular fa-calendar text-[#19A389]"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Laporan Kehadiran Mata Pelajaran</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Rekap Absensi Siswa</h2>
                            @if($adaFilter)
                                <p class="text-xs text-white/90 mt-1.5 font-medium">
                                    @if($semua)
                                        Seluruh Kelas yang Diajar <span class="text-white/50 mx-1.5">•</span>
                                    @else
                                        Kelas <b class="font-extrabold">{{ $terpilih['kelas'] }}</b> <span class="text-white/50 mx-1.5">•</span>
                                        Mapel: <b class="font-extrabold">{{ $terpilih['mapel'] }}</b> <span class="text-white/50 mx-1.5">•</span>
                                    @endif
                                    Guru Pengampu: <b class="font-extrabold">{{ $guru->nama ?? Auth::user()->name }}</b>
                                </p>
                            @else
                                <p class="text-xs text-white/80 mt-1">Pantau rekapitulasi kehadiran siswa per kelas dan mata pelajaran</p>
                            @endif
                        </div>

                        @if($adaFilter)
                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-3.5 py-2 rounded-2xl text-xs font-semibold inline-flex items-center gap-2">
                                    <i class="fa-solid fa-users"></i>
                                    <span>{{ $rekap->count() }} Siswa</span>
                                </div>
                                @unless($semua)
                                    <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-3.5 py-2 rounded-2xl text-xs font-semibold inline-flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <span>{{ $efektif }} Pertemuan</span>
                                    </div>
                                @endunless
                            </div>
                        @endif
                    </div>

                    <!-- CARD UTAMA REKAP -->
                    <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                        <!-- ============ FILTER STRIP SEJAJAR DINAMIS ============ -->
                        <form method="GET" action="{{ route('guru.rekap-siswa') }}" id="formFilter" class="flex flex-wrap items-center justify-between gap-3 text-xs">

                            <input type="hidden" name="mode" id="filterMode" value="{{ $modeAktif }}">

                            <div class="flex flex-wrap items-center gap-2.5 flex-1">
                                <!-- SELECT KELAS & MAPEL -->
                                <div class="relative min-w-[210px] w-full sm:w-auto max-w-xs">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#19A389]">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                    </span>
                                    <select name="kelas_mapel" class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white appearance-none transition">
                                        @if($opsi->isNotEmpty())
                                            <option value="ALL" {{ $semua ? 'selected' : '' }}>Semua Kelas yang Saya Ajar</option>
                                        @endif
                                        @forelse($opsi as $o)
                                            <option value="{{ $o['key'] }}" {{ $adaFilter && ! $semua && $terpilih['key'] === $o['key'] ? 'selected' : '' }}>
                                                Kelas {{ $o['kelas'] }} - {{ $o['mapel'] }}
                                            </option>
                                        @empty
                                            <option value="">Belum ada jadwal</option>
                                        @endforelse
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </span>
                                </div>

                                <!-- MODE 1: PER BULAN -->
                                <div id="boxBulan" class="relative min-w-[180px] w-full sm:w-auto {{ $modeAktif === 'bulan' ? '' : 'hidden' }}">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </span>
                                    <input type="month" name="bulan" value="{{ $valBulan }}" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white cursor-pointer transition">
                                </div>

                                <!-- MODE 2: PER TANGGAL -->
                                <div id="boxTanggal" class="relative min-w-[180px] w-full sm:w-auto {{ $modeAktif === 'tanggal' ? '' : 'hidden' }}">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </span>
                                    <input type="date" name="tgl" value="{{ $valTgl }}" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white cursor-pointer transition">
                                </div>

                                <!-- MODE 3: RENTANG PEMBELAJARAN (ADA OPSI ADMIN & MANUAL) -->
                                <div id="boxRentang" class="flex flex-wrap items-center gap-2 w-full sm:w-auto {{ $modeAktif === 'rentang' ? '' : 'hidden' }}">
                                    <!-- SELECT PERIODE DARI ADMIN -->
                                    <div class="relative min-w-[200px] w-full sm:w-auto">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                                            <i class="fa-solid fa-layer-group"></i>
                                        </span>
                                        <select name="periode" id="selectPeriodeAdmin" onchange="handlePeriodeChange(this)" class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white appearance-none transition">
                                            <option value="custom" {{ empty($valPeriode) || $valPeriode === 'custom' ? 'selected' : '' }}>-- Custom (Isi Manual) --</option>
                                            @foreach($listPeriode as $p)
                                                <option value="{{ $p->id }}"
                                                    data-mulai="{{ \Carbon\Carbon::parse($p->tanggal_mulai ?? $p->mulai)->format('Y-m-d') }}"
                                                    data-selesai="{{ \Carbon\Carbon::parse($p->tanggal_selesai ?? $p->selesai)->format('Y-m-d') }}"
                                                    {{ (string)$valPeriode === (string)$p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_periode ?? $p->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-xs"></i>
                                        </span>
                                    </div>

                                    <!-- INPUT DARI & SAMPAI -->
                                    <div id="boxCustomRentang" class="flex items-center gap-2 w-full sm:w-auto">
                                        <div class="relative flex-1 sm:min-w-[135px]">
                                            <input type="date" name="dari" id="inputDari" value="{{ $valDari }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-base sm:text-xs focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        </div>
                                        <span class="font-bold text-slate-400">-</span>
                                        <div class="relative flex-1 sm:min-w-[135px]">
                                            <input type="date" name="sampai" id="inputSampai" value="{{ $valSampai }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-base sm:text-xs focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB PILL FILTER WAKTU -->
                                <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 font-bold text-slate-500 text-xs">
                                    <button type="button" onclick="setFilterMode('bulan')" id="btnBulan" class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'bulan' ? 'bg-white text-[#0D6353] shadow-sm' : 'hover:text-slate-800' }}">Per Bulan</button>
                                    <button type="button" onclick="setFilterMode('tanggal')" id="btnTanggal" class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'tanggal' ? 'bg-white text-[#0D6353] shadow-sm' : 'hover:text-slate-800' }}">Per Tanggal</button>
                                    <button type="button" onclick="setFilterMode('rentang')" id="btnRentang" class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'rentang' ? 'bg-white text-[#0D6353] shadow-sm' : 'hover:text-slate-800' }}">Rentang</button>
                                </div>
                            </div>

                            <!-- TOMBOL TERAPKAN -->
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2">
                                <i class="fa-solid fa-filter"></i>
                                <span>Terapkan</span>
                            </button>
                        </form>

                        @if(! $adaFilter)
                            <div class="p-10 text-center text-slate-400 text-xs italic border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                                <i class="fa-regular fa-calendar-xmark text-2xl block mb-2 text-slate-300"></i>
                                Anda belum memiliki jadwal mengajar, sehingga belum ada rekap yang dapat ditampilkan.<br>
                                Hubungi admin untuk mengatur jadwal.
                            </div>
                        @else

                            <hr class="border-slate-100">

                            <!-- ============ PERINGATAN BELUM DIABSEN ============ -->
                            @if($belumCount > 0)
                                <div class="p-3.5 bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold rounded-2xl flex items-start gap-3">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base mt-0.5 shrink-0"></i>
                                    <span>Perhatian: Terdapat <b>{{ $belumCount }}</b> jadwal pertemuan yang belum diabsen (kolom kuning, tanda "?").
                                        @if($semua)
                                            Untuk absen susulan, pilih satu kelas pada filter lalu klik tanggal yang berwarna kuning.
                                        @else
                                            Klik tanggal berwarna kuning untuk absen susulan. Hanya guru yang bersangkutan (guru pengganti bila ada) yang dapat mengisi.
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <!-- ============ INFORMASI PERIODE INI ============ -->
                            <div class="p-3 bg-slate-50 border border-slate-200/70 rounded-2xl text-xs text-slate-600 flex items-start sm:items-center gap-2.5 font-semibold">
                                <i class="fa-solid fa-thumbtack text-rose-500 mt-0.5 sm:mt-0 shrink-0"></i>
                                <span><b>Informasi Periode Ini:</b>
                                @if($liburRentang->isNotEmpty())
                                    Ada {{ $liburRentang->count() }} hari libur nasional/sekolah pada rentang ini
                                    ({{ $liburRentang->map(fn ($l) => Absen::namaHari($l->tanggal) . ', ' . $l->tanggal->format('d/m/Y') . ' - ' . $l->keterangan)->implode('; ') }}).
                                @else
                                    Tidak ada hari libur nasional pada rentang ini.
                                @endif
                                @unless($semua)
                                    Hari efektif: <b class="text-slate-900">{{ $efektif }}</b> pertemuan.
                                @endunless
                                </span>
                            </div>

                            <!-- ============ TABEL MATRIKS REKAP MAPEL ============ -->
                            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                                <table class="w-full text-center text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-[#0f7a63] text-white font-bold text-[11px]">
                                            <th rowspan="3" class="px-3 py-3 w-10 text-center border-r border-emerald-700/60">No</th>
                                            <th rowspan="3" class="px-4 py-3 text-left min-w-[140px] border-r border-emerald-700/60">Nama Siswa</th>
                                            <th rowspan="3" class="px-4 py-3 text-left border-r border-emerald-700/60">NISN</th>
                                            @if($semua)
                                                <th rowspan="3" class="px-3 py-3 text-left border-r border-emerald-700/60">Kelas</th>
                                            @endif

                                            @foreach($bulanGrup as $ym => $grup)
                                                @php $first = $grup->first()->carbon; @endphp
                                                <th colspan="{{ $grup->count() }}" class="px-2 py-2 border-b border-r border-emerald-700/60">
                                                    {{ $bulanPendek[$first->month - 1] }} {{ $first->year }}
                                                </th>
                                            @endforeach

                                            <th colspan="4" class="px-3 py-2 border-b border-r border-emerald-700/60">Total</th>
                                            <th rowspan="3" class="px-3 py-3 text-center min-w-[56px]">%<br>Hadir</th>
                                        </tr>

                                        <tr class="bg-[#0f7a63] text-white font-bold text-[10px]">
                                            @foreach($kolom as $k)
                                                @php
                                                    $bgHead = match($k->state) {
                                                        'belum' => 'bg-yellow-400 text-slate-900',
                                                        'libur' => 'bg-rose-500 text-white',
                                                        default => 'bg-[#0f7a63] text-white',
                                                    };
                                                @endphp
                                                <th class="p-1 w-7 min-w-[28px] border-r border-emerald-700/60 {{ $bgHead }}"
                                                    title="{{ $k->carbon->format('d/m/Y') }}{{ $k->state === 'libur' ? ' - Libur: ' . $k->keterangan : '' }}{{ $k->state === 'belum' ? ' - Belum diabsen' : '' }}">
                                                    @if($semua)
                                                        <div class="py-0.5">{{ $k->carbon->format('d') }}</div>
                                                    @else
                                                        <button type="button" class="btn-kolom w-full py-0.5 hover:underline" data-tanggal="{{ $k->tanggal }}">{{ $k->carbon->format('d') }}</button>
                                                    @endif
                                                </th>
                                            @endforeach

                                            <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/60">H</th>
                                            <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/60">I</th>
                                            <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/60">S</th>
                                            <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/60">A</th>
                                        </tr>

                                        <tr class="bg-[#0f7a63] text-white font-medium text-[9px]">
                                            @foreach($kolom as $k)
                                                @php
                                                    $bgHead = match($k->state) {
                                                        'belum' => 'bg-yellow-400 text-slate-700',
                                                        'libur' => 'bg-rose-500 text-white',
                                                        default => 'bg-[#0f7a63] text-emerald-100',
                                                    };
                                                @endphp
                                                <th class="py-1 border-r border-emerald-700/60 {{ $bgHead }}">{{ $hurufHari[$k->carbon->dayOfWeekIso - 1] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-200 bg-white text-slate-700 text-xs">
                                        @forelse($rekap as $i => $r)
                                            <tr class="hover:bg-teal-50/40 transition">
                                                <td class="px-3 py-2.5 font-medium text-slate-500 border-r border-slate-200">{{ $i + 1 }}</td>
                                                <td class="px-4 py-2.5 text-left font-bold text-slate-800 border-r border-slate-200 whitespace-nowrap">{{ $r->nama }}</td>
                                                <td class="px-4 py-2.5 text-left text-slate-400 font-mono text-[11px] border-r border-slate-200">{{ $r->nisn ?? '-' }}</td>
                                                @if($semua)
                                                    <td class="px-3 py-2.5 text-left whitespace-nowrap border-r border-slate-200">
                                                        <span class="font-bold text-slate-700">{{ $r->kelas }}</span>
                                                        @if($mapelBanyak)<span class="block text-[9px] text-slate-400">{{ $r->mapel }}</span>@endif
                                                    </td>
                                                @endif

                                                @foreach($kolom as $tgl => $k)
                                                    @php
                                                        $stSel = $r->st[$tgl] ?? 'kosong';
                                                        $nilai = $r->sel[$tgl] ?? null;
                                                    @endphp

                                                    <td class="px-1 py-1.5 text-center border-r border-slate-200">
                                                        @if($stSel === 'libur')
                                                            <span class="text-rose-500 font-bold">L</span>
                                                        @elseif($stSel === 'belum')
                                                            <span class="text-yellow-600 font-bold">?</span>
                                                        @elseif($stSel === 'sudah' && $nilai)
                                                            <span class="{{ $gayaHuruf[$nilai] ?? '' }}">{{ $nilai }}</span>
                                                        @else
                                                            <span class="text-slate-300 font-bold">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach

                                                <td class="px-2 py-2.5 font-semibold text-slate-700 border-r border-slate-200">{{ $r->total['H'] }}</td>
                                                <td class="px-2 py-2.5 font-semibold text-slate-700 border-r border-slate-200">{{ $r->total['I'] }}</td>
                                                <td class="px-2 py-2.5 font-semibold text-slate-700 border-r border-slate-200">{{ $r->total['S'] }}</td>
                                                <td class="px-2 py-2.5 font-semibold text-slate-700 border-r border-slate-200">{{ $r->total['A'] }}</td>
                                                <td class="px-3 py-2.5 font-black text-slate-900">{{ $fmtPersen($r->persen) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ $kolomTetap + $kolom->count() + 5 }}" class="p-8 text-slate-400 italic">
                                                    Belum ada siswa terdaftar di kelas ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- LEGENDA STATUS -->
                            <div class="bg-[#EBF7F4] border border-[#D1EFE8] rounded-2xl p-3.5 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-700">
                                <div class="flex items-center gap-1.5"><span class="bg-[#D1F2D9] text-[#137A43] font-bold px-2 py-0.5 rounded-md">H</span> Hadir</div>
                                <div class="flex items-center gap-1.5"><span class="bg-[#D0EBFD] text-[#1D6F8A] font-bold px-2 py-0.5 rounded-md">I</span> Izin</div>
                                <div class="flex items-center gap-1.5"><span class="bg-[#FEF3D6] text-[#A16207] font-bold px-2 py-0.5 rounded-md">S</span> Sakit</div>
                                <div class="flex items-center gap-1.5"><span class="bg-[#FDE2E2] text-[#991B1B] font-bold px-2 py-0.5 rounded-md">A</span> Alpa</div>
                                <div class="flex items-center gap-1.5"><span class="text-slate-400 font-bold px-1.5">-</span> Belum diabsen</div>
                            </div>

                            <p class="text-[11px] text-slate-400 leading-relaxed px-1">
                                Arahkan kursor ke tanggal untuk melihat guru yang menyelesaikan Absensi Utama. Rekap ini terpisah dari rekap mata pelajaran.
                            </p>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    @if($adaFilter && ! $semua)
        <!-- MODAL DETAIL TANGGAL -->
        <div id="modalTanggal" class="hidden fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-md border border-slate-100">
                <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-[#0D6353] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Detail Pertemuan</h3>
                    </div>
                    <button type="button" id="mtTutup" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Kelas & Mapel</label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">Kelas {{ $terpilih['kelas'] }} - {{ $terpilih['mapel'] }}</div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1">Tanggal</label>
                        <div class="p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-800 flex justify-between items-center">
                            <span id="mtTanggal"></span><i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                    </div>
                    <div id="mtStatus" class="p-3 rounded-xl font-semibold"></div>
                    <div id="mtAksi" class="flex flex-wrap gap-2 pt-1"></div>
                </div>
            </div>
        </div>

        <!-- FORM HAPUS HARI -->
        <form id="formHapusHari" method="POST" action="{{ route('guru.rekap-siswa.hapus-hari') }}" class="hidden">
            @csrf
            @method('DELETE')
            <input type="hidden" name="kelas" value="{{ $terpilih['kelas'] }}">
            <input type="hidden" name="mapel" value="{{ $terpilih['mapel'] }}">
            <input type="hidden" name="tanggal" id="hapusTanggal">
            <input type="hidden" name="kelas_mapel" value="{{ $terpilih['key'] }}">
            <input type="hidden" name="periode" value="{{ $valPeriode }}">
            <input type="hidden" name="dari" value="{{ $valDari }}">
            <input type="hidden" name="sampai" value="{{ $valSampai }}">
            <input type="hidden" name="mode" value="{{ $modeAktif }}">
            <input type="hidden" name="bulan" value="{{ $valBulan }}">
            <input type="hidden" name="tanggal_filter" value="{{ $valTgl }}">
        </form>
    @endif

    <div id="dataDetail" class="hidden" data-detail="{{ json_encode($detailKolom) }}"></div>

    @if(session('success') || session('error'))
        <div id="flashData" class="hidden"
             data-tipe="{{ session('success') ? 'success' : 'error' }}"
             data-pesan="{{ session('success') ?? session('error') }}"></div>
    @endif

    <script>
        // FUNGSI SWITCH FILTER WAKTU (PER BULAN, PER TANGGAL, RENTANG)
        function setFilterMode(mode) {
            document.getElementById('filterMode').value = mode;

            const boxBulan = document.getElementById('boxBulan');
            const boxTanggal = document.getElementById('boxTanggal');
            const boxRentang = document.getElementById('boxRentang');

            const btnBulan = document.getElementById('btnBulan');
            const btnTanggal = document.getElementById('btnTanggal');
            const btnRentang = document.getElementById('btnRentang');

            boxBulan.classList.add('hidden');
            boxTanggal.classList.add('hidden');
            boxRentang.classList.add('hidden');

            const activeClass = 'bg-white text-[#0D6353] shadow-sm';
            const inactiveClass = 'hover:text-slate-800';

            [btnBulan, btnTanggal, btnRentang].forEach(btn => {
                btn.className = 'px-3.5 py-2 rounded-lg transition ' + inactiveClass;
            });

            if (mode === 'bulan') {
                boxBulan.classList.remove('hidden');
                btnBulan.className = 'px-3.5 py-2 rounded-lg transition ' + activeClass;
            } else if (mode === 'tanggal') {
                boxTanggal.classList.remove('hidden');
                btnTanggal.className = 'px-3.5 py-2 rounded-lg transition ' + activeClass;
            } else if (mode === 'rentang') {
                boxRentang.classList.remove('hidden');
                btnRentang.className = 'px-3.5 py-2 rounded-lg transition ' + activeClass;
            }
        }

        // FUNGSI AUTO-FILL TANGGAL PERIODE DARI ADMIN
        function handlePeriodeChange(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            const mulai = opt.dataset.mulai;
            const selesai = opt.dataset.selesai;

            if (mulai && selesai) {
                document.getElementById('inputDari').value = mulai;
                document.getElementById('inputSampai').value = selesai;
            }
        }

        // MODAL DETAIL TANGGAL
        const DETAIL = JSON.parse(document.getElementById('dataDetail').dataset.detail || '{}');
        const modalTanggal = document.getElementById('modalTanggal');

        function buatTombol(teks, kelas, aksi) {
            const el = document.createElement(aksi.href ? 'a' : 'button');
            el.className = 'px-4 py-2.5 rounded-xl font-bold text-xs transition ' + kelas;
            el.textContent = teks;
            if (aksi.href) { el.href = aksi.href; } else { el.type = 'button'; el.addEventListener('click', aksi.klik); }
            return el;
        }

        if (modalTanggal) {
            document.querySelectorAll('.btn-kolom').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const d = DETAIL[btn.dataset.tanggal];
                    if (!d) return;

                    document.getElementById('mtTanggal').textContent = d.tampil;
                    const status = document.getElementById('mtStatus');
                    const aksi = document.getElementById('mtAksi');
                    aksi.replaceChildren();

                    if (d.state === 'sudah') {
                        status.className = 'p-3 rounded-xl font-semibold bg-emerald-50 border border-emerald-200 text-emerald-900';
                        status.textContent = 'Sudah diabsen.';
                        aksi.append(buatTombol('Hapus Harian (seluruh siswa)', 'bg-rose-600 hover:bg-rose-700 text-white', {
                            klik: function () { modalTanggal.classList.add('hidden'); hapusHari(btn.dataset.tanggal, d.tglPendek); }
                        }));
                    } else if (d.state === 'belum') {
                        status.className = 'p-3 rounded-xl font-semibold bg-yellow-100 border border-yellow-300 text-yellow-900';
                        status.textContent = 'Belum Diabsen.';
                        if (d.urlSusulan) {
                            aksi.append(buatTombol('Lanjut Absen Susulan', 'bg-[#19A389] hover:bg-[#138A73] text-white', { href: d.urlSusulan }));
                        } else if (d.penanggung) {
                            const info = document.createElement('div');
                            info.className = 'w-full p-3 rounded-xl bg-slate-800 text-white font-semibold';
                            info.textContent = d.penanggung.pengganti
                                ? 'Terkunci: hanya Guru Pengganti (' + d.penanggung.nama + ') yang dapat mengabsen tanggal ini.'
                                : 'Terkunci: hanya guru pengampu (' + d.penanggung.nama + ') yang dapat mengabsen tanggal ini.';
                            aksi.append(info);
                        }
                    } else if (d.state === 'libur') {
                        status.className = 'p-3 rounded-xl font-semibold bg-rose-50 border border-rose-200 text-rose-800';
                        status.textContent = 'Libur' + (d.keterangan ? ': ' + d.keterangan : '') + '. Tidak dihitung sebagai hari efektif.';
                    } else {
                        status.className = 'p-3 rounded-xl font-semibold bg-slate-50 border border-slate-200 text-slate-600';
                        status.textContent = 'Hari ini. Absen dilakukan dari kartu jadwal di Dashboard.';
                    }

                    modalTanggal.classList.remove('hidden');
                });
            });

            document.getElementById('mtTutup').addEventListener('click', function () { modalTanggal.classList.add('hidden'); });
            modalTanggal.addEventListener('click', function (e) { if (e.target === modalTanggal) modalTanggal.classList.add('hidden'); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modalTanggal.classList.contains('hidden')) modalTanggal.classList.add('hidden');
            });
        }

        // Hapus Absen Harian
        function hapusHari(tanggalIso, tanggalTampil) {
            Swal.fire({
                title: 'Hapus absen tanggal ' + tanggalTampil + '?',
                html: 'Absen <strong>seluruh siswa</strong> pada tanggal ini akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#CE1B1B',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Harian',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold',
                    cancelButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold'
                }
            }).then((hasil) => {
                if (hasil.isConfirmed) {
                    document.getElementById('hapusTanggal').value = tanggalIso;
                    document.getElementById('formHapusHari').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const flash = document.getElementById('flashData');
            if (!flash) return;
            Swal.fire({
                icon: flash.dataset.tipe === 'success' ? 'success' : 'error',
                title: flash.dataset.tipe === 'success' ? 'Berhasil' : 'Gagal',
                text: flash.dataset.pesan,
                confirmButtonText: 'OK',
                confirmButtonColor: '#19A389',
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                    confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold shadow-md'
                }
            });
        });
    </script>
</body>
</html>