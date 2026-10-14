<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail SPP - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            'dibuat' => 'bg-emerald-100 text-emerald-700',
            'diubah' => 'bg-amber-100 text-amber-700',
            'dihapus' => 'bg-rose-100 text-rose-700',
        ];
    @endphp

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">AUDIT TRAIL SPP</h1>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 mt-4">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-clock-rotate-left text-[#24B9A1]"></i> Riwayat Aktivitas SPP
                            </h2>
                            <p class="text-xs text-slate-400 mt-1">Catatan bersifat permanen dan tidak dapat diubah atau dihapus dari aplikasi.</p>
                        </div>

                        <form method="GET" action="{{ route('admin.spp.audit') }}" class="flex flex-wrap items-center gap-2 text-xs w-full sm:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Kuitansi / nama petugas..." class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                            <select name="aksi" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                                <option value="">Semua Aksi</option>
                                @foreach(['dibuat' => 'Dibuat', 'diubah' => 'Diubah', 'dihapus' => 'Dihapus'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ request('aksi') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="p-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition"><i class="fa-solid fa-filter"></i></button>
                            @if(request('search') || request('aksi'))
                                <a href="{{ route('admin.spp.audit') }}" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition" title="Reset Filter"><i class="fa-solid fa-rotate-right"></i></a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="p-3">Waktu (WIB)</th>
                                    <th class="p-3">Petugas</th>
                                    <th class="p-3">Aksi</th>
                                    <th class="p-3">No. Kuitansi</th>
                                    <th class="p-3">Rincian</th>
                                    <th class="p-3">Alasan</th>
                                    <th class="p-3">IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 align-top">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3 whitespace-nowrap">{{ $log->created_at->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i:s') }}</td>
                                        <td class="p-3 font-semibold text-slate-900">{{ $log->user_name ?? '-' }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $warnaAksi[$log->aksi] ?? 'bg-slate-100 text-slate-600' }}">{{ $log->aksi }}</span>
                                        </td>
                                        <td class="p-3 font-mono text-[11px] font-bold whitespace-nowrap">{{ $log->referensi ?? '-' }}</td>
                                        <td class="p-3">
                                            @if($log->aksi === 'diubah')
                                                <ul class="space-y-0.5">
                                                    @foreach(($log->data_baru ?? []) as $key => $baru)
                                                        <li>
                                                            <span class="text-slate-400">{{ $labelField[$key] ?? $key }}:</span>
                                                            <span class="line-through text-rose-500">{{ $tampilNilai($key, $log->data_lama[$key] ?? null) }}</span>
                                                            &rarr;
                                                            <span class="font-semibold text-emerald-700">{{ $tampilNilai($key, $baru) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                @php $data = $log->aksi === 'dihapus' ? ($log->data_lama ?? []) : ($log->data_baru ?? []); @endphp
                                                <ul class="space-y-0.5">
                                                    @foreach(['siswa', 'bulan', 'tahun', 'nominal', 'metode_pembayaran', 'keterangan'] as $key)
                                                        @if(array_key_exists($key, $data) && $data[$key] !== null && $data[$key] !== '')
                                                            <li><span class="text-slate-400">{{ $labelField[$key] }}:</span> {{ $tampilNilai($key, $data[$key]) }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td class="p-3 max-w-[200px]">{{ $log->alasan ?? '-' }}</td>
                                        <td class="p-3 text-slate-400 whitespace-nowrap">{{ $log->ip_address ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="p-6 text-center text-slate-400">Belum ada aktivitas SPP yang tercatat.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $logs->links() }}</div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
