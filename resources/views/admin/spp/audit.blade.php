<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail SPP - SMP Djojoredjo</title>
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
        $labelField = [
            'siswa' => 'Siswa', 'siswa_id' => 'ID Siswa', 'no_transaksi' => 'No. Kuitansi',
            'bulan' => 'Bulan', 'tahun' => 'Tahun', 'nominal' => 'Nominal',
            'metode_pembayaran' => 'Metode', 'tanggal_bayar' => 'Tanggal Bayar',
            'status' => 'Status', 'keterangan' => 'Keterangan', 'admin_id' => 'ID Petugas', 'id' => 'ID',
        ];
        $tampilNilai = function ($key, $val) {
            if ($val === null || $val === '') return '-';
            if ($key === 'nominal') return 'Rp ' . number_format((float) $val, 0, ',', '.');
            return (string) $val;
        };
        $warnaAksi = [
            'dibuat' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'diubah' => 'bg-amber-100 text-amber-700 border border-amber-200',
            'dihapus' => 'bg-rose-100 text-rose-700 border border-rose-200',
        ];
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
                        KEUANGAN • AUDIT TRAIL SPP
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                    </div>
                    @if(Route::has('admin.spp.index'))
                        <a href="{{ route('admin.spp.index') }}"
                           class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition">
                            <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                            <span>Pencatatan SPP</span>
                        </a>
                    @endif
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Keamanan & Transparansi Keuangan</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Audit Trail Pembayaran SPP</h2>
                            <p class="text-xs text-white/80 mt-1">Log permanen pembuatan, koreksi, dan penghapusan data SPP beserta alasan dan alamat IP petugas</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>{{ $logs->total() }} Log Tercatat</span>
                        </div>
                    </div>

                    <!-- CARD RIWAYAT AKTIVITAS SPP -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Header & Filter -->
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0 mt-0.5">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Riwayat Aktivitas SPP</h3>
                                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Catatan bersifat permanen (immutable) dan tidak dapat diubah atau dihapus dari aplikasi.</p>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.spp.audit') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                <div class="relative flex-1 sm:w-64">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Kuitansi / nama petugas..."
                                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>

                                <select name="aksi"
                                        class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">Semua Aksi</option>
                                    @foreach(['dibuat' => 'Dibuat', 'diubah' => 'Diubah', 'dihapus' => 'Dihapus'] as $val => $lbl)
                                        <option value="{{ $val }}" {{ request('aksi') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                        class="px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white rounded-xl text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-filter"></i>
                                    <span>Filter</span>
                                </button>
                                @if(request('search') || request('aksi'))
                                    <a href="{{ route('admin.spp.audit') }}"
                                       class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="font-mono text-[11px] font-extrabold text-[#138A73] bg-teal-50 px-2 py-0.5 rounded-md border border-teal-100">
                                                {{ $log->referensi ?? '-' }}
                                            </span>
                                            <p class="font-bold text-sm text-slate-900 mt-1">{{ $log->user_name ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-400">
                                                <i class="fa-regular fa-clock mr-1"></i>{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') }} WIB
                                            </p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase shrink-0 {{ $warnaAksi[$log->aksi] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $log->aksi }}
                                        </span>
                                    </div>

                                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-xs space-y-1">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Rincian Data</span>
                                        @if($log->aksi === 'diubah')
                                            <ul class="space-y-1">
                                                @foreach(($log->data_baru ?? []) as $key => $baru)
                                                    <li class="leading-relaxed">
                                                        <span class="text-slate-500 font-semibold">{{ $labelField[$key] ?? $key }}:</span>
                                                        <span class="line-through text-rose-500">{{ $tampilNilai($key, $log->data_lama[$key] ?? null) }}</span>
                                                        →
                                                        <span class="font-bold text-emerald-700">{{ $tampilNilai($key, $baru) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            @php $data = $log->aksi === 'dihapus' ? ($log->data_lama ?? []) : ($log->data_baru ?? []); @endphp
                                            <ul class="space-y-1">
                                                @foreach(['siswa', 'bulan', 'tahun', 'nominal', 'metode_pembayaran', 'keterangan'] as $key)
                                                    @if(array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '')
                                                        <li>
                                                            <span class="text-slate-500 font-semibold">{{ $labelField[$key] }}:</span>
                                                            <span class="text-slate-800 font-medium">{{ $tampilNilai($key, $data[$key]) }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>

                                    @if($log->alasan)
                                        <div class="p-2.5 bg-amber-50/80 border border-amber-200/70 rounded-xl text-[11px] text-amber-900">
                                            <span class="font-bold">Alasan:</span> {{ $log->alasan }}
                                        </div>
                                    @endif

                                    <div class="text-[11px] text-slate-400 font-mono">
                                        IP: {{ $log->ip_address ?? '-' }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-solid fa-clock-rotate-left text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada aktivitas SPP yang tercatat.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#19A389] text-white font-bold uppercase tracking-wider text-[11px]">
                                        <th class="p-3.5 whitespace-nowrap">Waktu (WIB)</th>
                                        <th class="p-3.5">Petugas</th>
                                        <th class="p-3.5 text-center">Aksi</th>
                                        <th class="p-3.5 whitespace-nowrap">No. Kuitansi</th>
                                        <th class="p-3.5">Rincian</th>
                                        <th class="p-3.5">Alasan</th>
                                        <th class="p-3.5">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 align-top">
                                    @forelse($logs as $log)
                                        <tr class="hover:bg-teal-50/40 transition">
                                            <td class="p-3.5 whitespace-nowrap font-medium text-slate-600">
                                                {{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') }}
                                            </td>
                                            <td class="p-3.5 font-bold text-slate-900">{{ $log->user_name ?? '-' }}</td>
                                            <td class="p-3.5 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $warnaAksi[$log->aksi] ?? 'bg-slate-100 text-slate-600' }}">
                                                    {{ $log->aksi }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 font-mono text-[11px] font-bold text-[#138A73] whitespace-nowrap">{{ $log->referensi ?? '-' }}</td>
                                            <td class="p-3.5">
                                                @if($log->aksi === 'diubah')
                                                    <ul class="space-y-1">
                                                        @foreach(($log->data_baru ?? []) as $key => $baru)
                                                            <li>
                                                                <span class="text-slate-400 font-medium">{{ $labelField[$key] ?? $key }}:</span>
                                                                <span class="line-through text-rose-500">{{ $tampilNilai($key, $log->data_lama[$key] ?? null) }}</span>
                                                                →
                                                                <span class="font-bold text-emerald-700">{{ $tampilNilai($key, $baru) }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    @php $data = $log->aksi === 'dihapus' ? ($log->data_lama ?? []) : ($log->data_baru ?? []); @endphp
                                                    <ul class="space-y-1">
                                                        @foreach(['siswa', 'bulan', 'tahun', 'nominal', 'metode_pembayaran', 'keterangan'] as $key)
                                                            @if(array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '')
                                                                <li>
                                                                    <span class="text-slate-400 font-medium">{{ $labelField[$key] }}:</span>
                                                                    <span class="font-semibold text-slate-700">{{ $tampilNilai($key, $data[$key]) }}</span>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </td>
                                            <td class="p-3.5 max-w-[220px] text-slate-600">{{ $log->alasan ?? '-' }}</td>
                                            <td class="p-3.5 font-mono text-[11px] text-slate-400 whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-slate-400 italic">Belum ada aktivitas SPP yang tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($logs->hasPages())
                            <div class="p-4 sm:px-6 border-t border-slate-100">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>