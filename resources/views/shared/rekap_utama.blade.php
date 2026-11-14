@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Utama - SMP Djojoredjo</title>
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
    $fmtPersen   = fn ($p) => $p === null ? '-' : rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . '%';
    $bulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $hurufHari   = ['Sn','Sl','Rb','Km','Jm','Sb','Mg'];
    $gayaHuruf   = [
        'H' => 'bg-[#D1F2D9] text-[#137A43] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'I' => 'bg-[#D0EBFD] text-[#1D6F8A] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'S' => 'bg-[#FEF3D6] text-[#A16207] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
        'A' => 'bg-[#FDE2E2] text-[#991B1B] font-bold px-2 py-0.5 rounded-md inline-block min-w-[24px]',
    ];

    $kolom       = $hasil['kolom'] ?? $kolom ?? collect();
    $bulanGrup   = $kolom->groupBy(fn ($k) => $k->carbon->format('Y-m'));
    $semuaAktif  = (bool) ($semua ?? false);
    $klikable    = ($bisaSusulan ?? true) && ! $semuaAktif;
    $belumCount  = $kolom->where('state', 'belum')->count();

    // Filter Mode & Values
    $modeAktif   = request('mode', $mode ?? 'bulan');
    $valBulan    = request('bulan', $bulan ?? \Carbon\Carbon::now()->format('Y-m'));
    $valTgl      = request('tgl', $tanggalPilih ?? \Carbon\Carbon::now()->format('Y-m-d'));
    $valPeriode  = request('periode', $periodeId ?? '');
    $valDari     = request('dari', $dari ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
    $valSampai   = request('sampai', $sampai ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));
    $listPeriode = $periodeList ?? [];

    $detailKolom = $klikable ? $kolom->mapWithKeys(fn ($k) => [$k->tanggal => [
        'tampil'     => Absen::namaHari($k->carbon) . ', ' . $k->carbon->format('d/m/Y'),
        'tglPendek'  => $k->carbon->format('d/m/Y'),
        'state'      => $k->state,
        'oleh'       => $k->oleh ?? null,
        'keterangan' => $k->keterangan ?? null,
        'urlSusulan' => ($k->susulan && ($k->susulan['boleh'] ?? false))
            ? route('guru.absen-utama.susulan', [
                $k->susulan['jadwal_id'],
                $k->tanggal,
                'kelas'   => $kelas,
                'periode' => $valPeriode,
                'dari'    => $valDari,
                'sampai'  => $valSampai,
                'mode'    => $modeAktif,
                'bulan'   => $valBulan,
                'tgl'     => $valTgl,
            ])
            : null,
        'penanggung' => $k->susulan ? [
            'nama'      => $k->susulan['nama'] ?? '-',
            'pengganti' => $k->susulan['pengganti'] ?? false,
        ] : null,
    ]])->all() : [];
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
                        PRESENSI SISWA • REKAP ABSENSI UTAMA
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <a href="{{ Route::has('admin.dashboard') && auth()->user()?->role === 'admin' ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}"
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
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Laporan Kehadiran Harian Siswa</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Rekap Absensi Utama (Sekolah)</h2>
                            <p class="text-xs text-white/80 mt-1">Absensi harian jam ke-1, dicatat sekali per kelas per hari efektif</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/25 px-4 py-2 rounded-2xl text-xs font-bold tracking-wide self-start sm:self-auto shrink-0">
                            {{ $semuaAktif ? 'Seluruh Kelas' : (isset($kelas) && $kelas ? 'Kelas ' . $kelas : 'Semua Kelas') }}
                        </div>
                    </div>

                    @if(isset($ringkasanHariIni))
                        <!-- STATUS HARI INI (PENGINGAT GURU JAM KE-1) -->
                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-slate-100">
                                <h3 class="text-sm font-bold text-slate-900">Status Absensi Utama Hari Ini</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                                    Dapat dilihat semua guru & admin. Kelas yang belum diisi: mohon ingatkan guru jam ke-1.
                                </p>
                            </div>

                            <!-- TAMPILAN HP STATUS HARI INI (< 768px) -->
                            <div class="block md:hidden divide-y divide-slate-100">
                                @forelse($ringkasanHariIni as $rh)
                                    <div class="p-4 space-y-2 hover:bg-slate-50/70 transition">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-bold text-sm text-slate-900">Kelas {{ $rh->kelas }}</span>
                                            <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-lg">
                                                Jam ke-1: {{ $rh->jam_masuk }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-600">
                                            Guru: <b class="text-slate-800">{{ $rh->guru ?: '-' }}</b>
                                            @if($rh->is_pengganti)
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 text-[10px] font-bold">Pengganti</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($rh->status === 'sudah')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                                    <i class="fa-solid fa-check mr-1"></i> Selesai oleh {{ $rh->oleh }}
                                                </span>
                                            @elseif($rh->status === 'belum')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-bold text-[10px]">
                                                    <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i> Belum diisi - ingatkan {{ $rh->guru }}
                                                </span>
                                            @elseif($rh->status === 'libur')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">Libur</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Belum waktunya</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400 italic text-xs">
                                        Tidak ada jadwal pelajaran hari ini.
                                    </div>
                                @endforelse
                            </div>

                            <!-- TAMPILAN TABLET & DESKTOP STATUS HARI INI (>= 768px) -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                        <tr>
                                            <th class="p-3.5">Kelas</th>
                                            <th class="p-3.5">Jam ke-1</th>
                                            <th class="p-3.5">Guru Jam ke-1</th>
                                            <th class="p-3.5">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        @forelse($ringkasanHariIni as $rh)
                                            <tr class="hover:bg-slate-50/90 transition">
                                                <td class="p-3.5 font-bold text-slate-800">Kelas {{ $rh->kelas }}</td>
                                                <td class="p-3.5 font-medium">{{ $rh->jam_masuk }}</td>
                                                <td class="p-3.5">
                                                    <span class="font-semibold text-slate-800">{{ $rh->guru ?: '-' }}</span>
                                                    @if($rh->is_pengganti)
                                                        <span class="ml-1 px-2 py-0.5 rounded-md bg-amber-200 text-amber-900 text-[10px] font-bold">Pengganti</span>
                                                    @endif
                                                </td>
                                                <td class="p-3.5">
                                                    @if($rh->status === 'sudah')
                                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                                            <i class="fa-solid fa-check mr-1"></i> Selesai oleh {{ $rh->oleh }}
                                                        </span>
                                                    @elseif($rh->status === 'belum')
                                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 font-bold text-[10px]">
                                                            <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i> Belum diisi - ingatkan {{ $rh->guru }}
                                                        </span>
                                                    @elseif($rh->status === 'libur')
                                                        <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">Libur</span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Belum waktunya</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="p-6 text-center text-slate-400 italic">Tidak ada jadwal pelajaran hari ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- CONTAINER FILTER DAN TABEL UTAMA -->
                    <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                        <!-- SEJAJAR INLINE FILTER STRIP -->
                        <form method="GET" action="{{ $aksi ?? route('guru.absen-utama.rekap') }}"
                              class="p-3.5 sm:p-4 bg-slate-50 border border-slate-100 rounded-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-3">

                            <input type="hidden" name="mode" id="filterMode" value="{{ $modeAktif }}">

                            <div class="flex flex-wrap items-center gap-2.5 flex-1">
                                <!-- SELECT KELAS (TANPA ICON DEKORATIF) -->
                                <div class="relative w-full sm:w-auto sm:min-w-[170px] sm:max-w-xs">
                                    <select name="kelas"
                                            class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] appearance-none shadow-2xs transition">
                                        @if(isset($kelasList) && $kelasList->isNotEmpty())
                                            <option value="ALL" {{ $semuaAktif ? 'selected' : '' }}>Semua Kelas</option>
                                            @foreach($kelasList as $k)
                                                <option value="{{ $k }}" {{ ! $semuaAktif && ($kelas ?? '') === $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                                            @endforeach
                                        @elseif(isset($opsi) && $opsi->isNotEmpty())
                                            <option value="ALL" {{ $semuaAktif ? 'selected' : '' }}>Semua Kelas</option>
                                            @foreach($opsi as $o)
                                                <option value="{{ $o['key'] }}" {{ ! $semuaAktif && isset($terpilih) && $terpilih['key'] === $o['key'] ? 'selected' : '' }}>Kelas {{ $o['kelas'] }} - {{ $o['mapel'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="ALL">Semua Kelas</option>
                                        @endif
                                    </select>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </span>
                                </div>

                                <!-- MODE 1: PER BULAN -->
                                <div id="boxBulan" class="relative w-full sm:w-auto sm:min-w-[180px] {{ $modeAktif === 'bulan' ? '' : 'hidden' }}">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#19A389]">
                                        <i class="fa-regular fa-calendar text-xs"></i>
                                    </span>
                                    <input type="month" name="bulan" value="{{ $valBulan }}"
                                           class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] shadow-2xs cursor-pointer transition">
                                </div>

                                <!-- MODE 2: PER TANGGAL -->
                                <div id="boxTanggal" class="relative w-full sm:w-auto sm:min-w-[180px] {{ $modeAktif === 'tanggal' ? '' : 'hidden' }}">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#19A389]">
                                        <i class="fa-regular fa-calendar text-xs"></i>
                                    </span>
                                    <input type="date" name="tgl" value="{{ $valTgl }}"
                                           class="w-full pl-9 pr-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] shadow-2xs cursor-pointer transition">
                                </div>

                                <!-- MODE 3: RENTANG PEMBELAJARAN -->
                                <div id="boxRentang" class="flex flex-wrap items-center gap-2 w-full sm:w-auto {{ $modeAktif === 'rentang' ? '' : 'hidden' }}">
                                    <div class="relative w-full sm:w-auto sm:min-w-[190px]">
                                        <select name="periode" id="selectPeriodeAdmin" onchange="handlePeriodeChange(this)"
                                                class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] appearance-none shadow-2xs transition">
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

                                    <div id="boxCustomRentang" class="flex items-center gap-1.5 w-full sm:w-auto">
                                        <input type="date" name="dari" id="inputDari" value="{{ $valDari }}"
                                               class="flex-1 sm:w-36 px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] shadow-2xs transition">
                                        <span class="font-bold text-slate-400 text-xs">-</span>
                                        <input type="date" name="sampai" id="inputSampai" value="{{ $valSampai }}"
                                               class="flex-1 sm:w-36 px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] shadow-2xs transition">
                                    </div>
                                </div>

                                <!-- TAB PILL FILTER WAKTU -->
                                <div class="bg-slate-200/70 p-1 rounded-xl grid grid-cols-3 sm:flex items-center gap-1 font-bold text-slate-500 text-xs w-full sm:w-auto">
                                    <button type="button" onclick="setFilterMode('bulan')" id="btnBulan"
                                            class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'bulan' ? 'bg-white text-[#0D6353] shadow-2xs' : 'hover:text-slate-800' }}">
                                        Per Bulan
                                    </button>
                                    <button type="button" onclick="setFilterMode('tanggal')" id="btnTanggal"
                                            class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'tanggal' ? 'bg-white text-[#0D6353] shadow-2xs' : 'hover:text-slate-800' }}">
                                        Per Tanggal
                                    </button>
                                    <button type="button" onclick="setFilterMode('rentang')" id="btnRentang"
                                            class="px-3.5 py-2 rounded-lg transition {{ $modeAktif === 'rentang' ? 'bg-white text-[#0D6353] shadow-2xs' : 'hover:text-slate-800' }}">
                                        Rentang
                                    </button>
                                </div>
                            </div>

                            <!-- TOMBOL TERAPKAN -->
                            <button type="submit"
                                    class="w-full lg:w-auto px-5 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 shrink-0">
                                <i class="fa-solid fa-filter"></i>
                                <span>Terapkan</span>
                            </button>
                        </form>

                        <!-- PERINGATAN ABSEN SUSULAN -->
                        @if($belumCount > 0)
                            <div class="p-3.5 bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold rounded-2xl flex items-start gap-3 leading-relaxed">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base mt-0.5 shrink-0"></i>
                                <span>
                                    Perhatian: Terdapat <b>{{ $belumCount }}</b> hari efektif yang belum diisi Absensi Utama (kolom kuning, tanda "<b>?</b>").
                                    @if($semuaAktif)
                                        Untuk mengisi absen susulan, pilih <b>satu kelas</b> spesifik pada filter lalu klik tanggal berwarna kuning.
                                    @else
                                        Klik angka tanggal yang berwarna kuning di tabel untuk melakukan <b>Absen Susulan</b>.
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- INFORMASI PERIODE -->
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600 font-semibold px-1">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-[#19A389]"></i>
                                <span>
                                    <b>Informasi Periode Ini:</b>
                                    @if(isset($hasil['liburRentang']) && $hasil['liburRentang']->isNotEmpty())
                                        Ada {{ $hasil['liburRentang']->count() }} hari libur.
                                    @else
                                        Tidak ada hari libur pada rentang ini.
                                    @endif
                                    Hari efektif: <b class="text-[#0D6353]">{{ $hasil['efektif'] ?? $efektif ?? 0 }}</b>.
                                </span>
                            </div>
                            <span class="text-[11px] text-slate-400">Geser tabel ke samping jika kolom tanggal banyak →</span>
                        </div>

                        <!-- TABEL MATRIKS REKAP -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-200">
                            <table class="w-full text-center text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#19A389] text-white font-bold text-[11px]">
                                        <th rowspan="3" class="px-3 py-3 w-10 text-center border-r border-emerald-700/40">No</th>
                                        <th rowspan="3" class="sticky left-0 z-10 bg-[#19A389] px-4 py-3 text-left min-w-[140px] border-r border-emerald-700/40 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.12)]">Nama Siswa</th>
                                        <th rowspan="3" class="px-4 py-3 text-left border-r border-emerald-700/40">NISN</th>
                                        @if($semuaAktif)
                                            <th rowspan="3" class="px-3 py-3 text-left border-r border-emerald-700/40">Kelas</th>
                                        @endif

                                        @foreach($bulanGrup as $grup)
                                            @php $first = $grup->first()->carbon; @endphp
                                            <th colspan="{{ $grup->count() }}" class="px-2 py-2 border-b border-r border-emerald-700/40">
                                                {{ $bulanPendek[$first->month - 1] }} {{ $first->year }}
                                            </th>
                                        @endforeach

                                        <th colspan="4" class="px-3 py-2 border-b border-r border-emerald-700/40">Total</th>
                                        <th rowspan="3" class="px-3 py-3 text-center min-w-[54px]">%<br>Hadir</th>
                                    </tr>

                                    <tr class="bg-[#158f78] text-white font-bold text-[10px]">
                                        @foreach($kolom as $k)
                                            @php
                                                $bg = match($k->state) {
                                                    'belum' => 'bg-yellow-400 text-slate-900',
                                                    'libur' => 'bg-rose-500 text-white',
                                                    default => 'bg-[#158f78] text-white'
                                                };
                                                $tooltip = $k->carbon->format('d/m/Y')
                                                    . ($k->state === 'libur' ? ' - Libur: ' . ($k->keterangan ?? '') : '')
                                                    . ($k->state === 'belum' ? ' - Belum diabsen (Klik untuk susulan)' : '')
                                                    . ($k->state === 'sudah' && !empty($k->oleh) ? ' - Selesai oleh ' . $k->oleh : '');
                                            @endphp
                                            <th class="p-1 w-7 min-w-[28px] border-r border-emerald-700/40 {{ $bg }}" title="{{ $tooltip }}">
                                                @if($klikable)
                                                    <button type="button" class="btn-kolom w-full py-0.5 hover:underline cursor-pointer font-bold" data-tanggal="{{ $k->tanggal }}">
                                                        {{ $k->carbon->format('d') }}
                                                    </button>
                                                @else
                                                    <div class="py-0.5">{{ $k->carbon->format('d') }}</div>
                                                @endif
                                            </th>
                                        @endforeach
                                        <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/40">H</th>
                                        <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/40">I</th>
                                        <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/40">S</th>
                                        <th rowspan="2" class="w-7 py-1 px-1 border-r border-emerald-700/40">A</th>
                                    </tr>

                                    <tr class="bg-[#0D6353] text-white font-medium text-[9px]">
                                        @foreach($kolom as $k)
                                            @php
                                                $bg = match($k->state) {
                                                    'belum' => 'bg-yellow-400 text-slate-900',
                                                    'libur' => 'bg-rose-500 text-white',
                                                    default => 'bg-[#0D6353] text-emerald-100'
                                                };
                                            @endphp
                                            <th class="py-1 border-r border-emerald-700/40 {{ $bg }}">{{ $hurufHari[$k->carbon->dayOfWeekIso - 1] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 bg-white text-slate-700 text-xs">
                                    @php $rekapData = $hasil['rekap'] ?? $rekap ?? []; @endphp
                                    @forelse($rekapData as $i => $r)
                                        <tr class="group hover:bg-slate-50 transition">
                                            <td class="px-3 py-2.5 font-medium text-slate-400 border-r border-slate-200">{{ $i + 1 }}</td>
                                            <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 px-4 py-2.5 text-left font-bold text-slate-800 border-r border-slate-200 whitespace-nowrap shadow-[2px_0_5px_-2px_rgba(0,0,0,0.06)]">{{ $r->nama }}</td>
                                            <td class="px-4 py-2.5 text-left text-slate-400 font-mono text-[11px] border-r border-slate-200">{{ $r->nisn ?? '-' }}</td>
                                            @if($semuaAktif)
                                                <td class="px-3 py-2.5 text-left font-bold text-slate-700 border-r border-slate-200 whitespace-nowrap">{{ $r->kelas }}</td>
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
                                                        <span class="text-amber-600 font-bold">?</span>
                                                    @elseif($stSel === 'sudah' && $nilai)
                                                        <span class="{{ $gayaHuruf[$nilai] ?? '' }}">{{ $nilai }}</span>
                                                    @else
                                                        <span class="text-slate-300 font-bold">-</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            <td class="px-2 py-2.5 font-bold text-emerald-700 border-r border-slate-200">{{ $r->total['H'] }}</td>
                                            <td class="px-2 py-2.5 font-bold text-sky-700 border-r border-slate-200">{{ $r->total['I'] }}</td>
                                            <td class="px-2 py-2.5 font-bold text-amber-700 border-r border-slate-200">{{ $r->total['S'] }}</td>
                                            <td class="px-2 py-2.5 font-bold text-rose-700 border-r border-slate-200">{{ $r->total['A'] }}</td>
                                            <td class="px-3 py-2.5 font-extrabold text-slate-900">{{ $fmtPersen($r->persen) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="p-8 text-center text-slate-400 italic">Belum ada siswa terdaftar di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- KETERANGAN -->
                        <div class="bg-[#EBF7F4] border border-[#D1EFE8] rounded-2xl p-3.5 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-700">
                            <div><span class="bg-[#D1F2D9] text-[#137A43] font-bold px-2 py-0.5 rounded mr-1">H</span> Hadir</div>
                            <div><span class="bg-[#D0EBFD] text-[#1D6F8A] font-bold px-2 py-0.5 rounded mr-1">I</span> Izin</div>
                            <div><span class="bg-[#FEF3D6] text-[#A16207] font-bold px-2 py-0.5 rounded mr-1">S</span> Sakit</div>
                            <div><span class="bg-[#FDE2E2] text-[#991B1B] font-bold px-2 py-0.5 rounded mr-1">A</span> Alpa</div>
                            <div><b class="text-rose-500 font-bold mr-1">L</b> Libur</div>
                            <div><b class="text-amber-600 font-bold mr-1">?</b> Belum diisi</div>
                            <div><b class="text-slate-400 font-bold mr-1">-</b> Belum waktunya</div>
                        </div>

                        <p class="text-[11px] text-slate-400 leading-relaxed px-1">
                            Pilih salah satu kelas lalu klik angka tanggal berwarna kuning untuk mengisi <b>Absensi Utama Susulan</b>. Rekap ini terpisah dari rekap mata pelajaran.
                        </p>
                    </div>

                </div>
            </div>
        </main>
    </div>

    @if($klikable)
        <!-- MODAL DETAIL PERTEMUAN & ABSEN SUSULAN -->
        <div id="modalTanggal" class="hidden fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-md border border-slate-100">
                <div class="flex justify-between items-start gap-3 mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Detail Absensi Utama</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Informasi status pengisian presensi harian</p>
                    </div>
                    <button type="button" id="mtTutup" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-3.5 pt-3 border-t border-slate-100 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Kelas</label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">Kelas {{ $kelas }}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 flex justify-between items-center">
                            <span id="mtTanggal"></span>
                            <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        </div>
                    </div>
                    <div id="mtStatus" class="p-3.5 rounded-2xl font-semibold leading-relaxed"></div>
                    <div id="mtAksi" class="flex flex-wrap gap-2 pt-1"></div>
                </div>
            </div>
        </div>
    @endif

    <div id="dataDetail" class="hidden" data-detail="{{ json_encode($detailKolom) }}"></div>

    @if(session('success') || session('error'))
        <div id="flashData" class="hidden"
             data-tipe="{{ session('success') ? 'success' : 'error' }}"
             data-pesan="{{ session('success') ?? session('error') }}"></div>
    @endif

    <script>
        // FUNGSI AKTIVASI & SWITCH FILTER WAKTU
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

            const activeClass = 'bg-white text-[#0D6353] shadow-2xs';
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

        // FUNGSI AUTO-FILL TANGGAL DARI PILIHAN PERIODE ADMIN
        function handlePeriodeChange(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            const mulai = opt.dataset.mulai;
            const selesai = opt.dataset.selesai;

            if (mulai && selesai) {
                document.getElementById('inputDari').value = mulai;
                document.getElementById('inputSampai').value = selesai;
            }
        }

        // MODAL DETAIL TANGGAL & TOMBOL ABSEN SUSULAN
        const DETAIL = JSON.parse(document.getElementById('dataDetail').dataset.detail || '{}');
        const modalTanggal = document.getElementById('modalTanggal');

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
                        status.className = 'p-3.5 rounded-2xl font-semibold leading-relaxed bg-emerald-50 border border-emerald-200 text-emerald-900';
                        status.textContent = 'Sudah diisi' + (d.oleh ? ' oleh ' + d.oleh : '') + '.';
                    } else if (d.state === 'belum') {
                        status.className = 'p-3.5 rounded-2xl font-semibold leading-relaxed bg-amber-50 border border-amber-300 text-amber-900';
                        status.textContent = 'Belum diisi Absensi Utama pada tanggal ini.';

                        if (d.urlSusulan) {
                            const link = document.createElement('a');
                            link.href = d.urlSusulan;
                            link.className = 'w-full justify-center px-4 py-3 rounded-xl font-extrabold text-xs transition bg-[#19A389] hover:bg-[#138A73] text-white inline-flex items-center gap-2 shadow-md';
                            link.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Lanjut Absen Susulan';
                            aksi.append(link);
                        } else if (d.penanggung) {
                            const info = document.createElement('div');
                            info.className = 'w-full p-3.5 rounded-2xl bg-slate-800 text-white font-semibold leading-relaxed';
                            info.textContent = d.penanggung.pengganti
                                ? 'Terkunci: hanya Guru Pengganti jam ke-1 (' + d.penanggung.nama + ') yang dapat mengisi susulan.'
                                : 'Terkunci: hanya Guru jam ke-1 (' + d.penanggung.nama + ') yang dapat mengisi susulan.';
                            aksi.append(info);
                        }
                    } else if (d.state === 'libur') {
                        status.className = 'p-3.5 rounded-2xl font-semibold leading-relaxed bg-rose-50 border border-rose-200 text-rose-800';
                        status.textContent = 'Hari Libur' + (d.keterangan ? ': ' + d.keterangan : '') + '. Tidak dihitung sebagai hari efektif.';
                    } else {
                        status.className = 'p-3.5 rounded-2xl font-semibold leading-relaxed bg-slate-50 border border-slate-200 text-slate-600';
                        status.textContent = 'Absensi untuk hari ini dilakukan melalui kartu jadwal jam ke-1 di Dashboard.';
                    }

                    modalTanggal.classList.remove('hidden');
                });
            });

            const closeModalTanggal = () => modalTanggal.classList.add('hidden');
            document.getElementById('mtTutup').addEventListener('click', closeModalTanggal);
            modalTanggal.addEventListener('click', function (e) { if (e.target === modalTanggal) closeModalTanggal(); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModalTanggal(); });
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