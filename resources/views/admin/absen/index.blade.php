<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen p-6 sm:p-8">
            <div class="flex flex-col xl:flex-row justify-between xl:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Rekap Absensi Guru</h1>
                    <p class="text-xs text-slate-500">Daftar kehadiran harian guru SMP Djojoredjo</p>
                </div>

                <!-- Form Filter Pencarian dan Tanggal -->
                <form action="{{ route('admin.absen') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    
                    <!-- Search by NUPTK / Nama -->
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NUPTK..." class="p-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#19A389] min-w-[180px]">

                    <!-- Filter Bulan -->
                    <select name="bulan" class="p-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#19A389]">
                        <option value="">-- Semua Bulan --</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('bulan') == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Filter Tahun -->
                    <select name="tahun" class="p-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#19A389]">
                        <option value="">-- Semua Tahun --</option>
                        @php $currentYear = date('Y'); @endphp
                        @foreach(range($currentYear - 2, $currentYear + 1) as $y)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-[10px] text-slate-400 font-bold px-1">ATAU</span>

                    <!-- Filter by Tanggal Harian -->
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#19A389]" title="Pilih Tanggal Spesifik">
                    
                    <button type="submit" class="bg-[#19A389] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#138A73] shadow-sm transition">
                        Filter
                    </button>
                    
                    @if(request('tanggal') || request('search') || request('bulan') || request('tahun'))
                        <a href="{{ route('admin.absen') }}" class="bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-xs font-semibold hover:bg-slate-300 transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-3.5 w-12 text-center">No</th>
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Nama Guru</th>
                                <th class="p-3.5">NUPTK / NIP</th>
                                <th class="p-3.5 text-center">Jam Masuk</th>
                                <th class="p-3.5 text-center">Jam Keluar</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($absensis as $index => $absen)
                                <tr class="hover:bg-teal-50/40 transition">
                                    <td class="p-3.5 text-center font-semibold text-slate-500">{{ $absensis->firstItem() + $index }}</td>
                                    <td class="p-3.5 font-medium text-slate-700">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="p-3.5 font-bold text-slate-800">{{ $absen->nama_guru }}</td>
                                    <td class="p-3.5 text-[#138A73] font-semibold">{{ $absen->nuptk_nip }}</td>
                                    <td class="p-3.5 text-center font-bold text-emerald-600">
                                        {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="p-3.5 text-center font-bold text-rose-600">
                                        {{ $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar)->format('H:i') . ' WIB' : '-' }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                            {{ $absen->status == 'Hadir' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ $absen->status == 'Izin' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                            {{ $absen->status == 'Sakit' ? 'bg-rose-100 text-rose-700 border border-rose-200' : '' }}">
                                            {{ $absen->status ?? 'Hadir' }}
                                        </span>
                                    </td>
                                    <td class="p-3.5">{{ $absen->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-6 text-center text-slate-400 italic">Belum ada data presensi tersimpan pada filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">
                    {{ $absensis->links() }}
                </div>
            </div>
        </main>
    </div>

</body>
</html>