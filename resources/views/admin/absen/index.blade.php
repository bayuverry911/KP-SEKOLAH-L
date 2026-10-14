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

    @php
        // Tampilkan NUPTK dari catatan presensi; jika kosong/"-", pakai data guru
        $nuptkTampil = function ($absen) use ($nuptkGuru) {
            $nilai = $absen->nuptk_nip;
            if ($nilai && $nilai !== '-') {
                return $nilai;
            }
            return $nuptkGuru[$absen->user_id] ?? '-';
        };

    @endphp

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

            <!-- RINGKASAN (mengikuti filter yang aktif) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Presensi</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $ringkasan['total'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-emerald-200 shadow-sm">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Hadir</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $ringkasan['Hadir'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-amber-200 shadow-sm">
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Izin</p>
                    <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $ringkasan['Izin'] }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-rose-200 shadow-sm">
                    <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Sakit</p>
                    <p class="text-2xl font-extrabold text-rose-700 mt-1">{{ $ringkasan['Sakit'] }}</p>
                </div>
            </div>

            <!-- GURU YANG BELUM PRESENSI -->
            @if($tampilBelumAbsen)
                <div class="mb-6 p-4 rounded-2xl border {{ $belumAbsen->isEmpty() ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} text-xs">
                    <p class="font-bold {{ $belumAbsen->isEmpty() ? 'text-emerald-700' : 'text-amber-700' }} mb-1.5">
                        <i class="fa-solid {{ $belumAbsen->isEmpty() ? 'fa-circle-check' : 'fa-user-clock' }} mr-1"></i>
                        @if($belumAbsen->isEmpty())
                            Semua guru sudah melakukan presensi pada {{ \Carbon\Carbon::parse($tanggalCek)->translatedFormat('d F Y') }}.
                        @else
                            {{ $belumAbsen->count() }} guru belum presensi pada {{ \Carbon\Carbon::parse($tanggalCek)->translatedFormat('d F Y') }}
                        @endif
                    </p>
                    @if($belumAbsen->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($belumAbsen as $g)
                                <span class="px-2.5 py-1 bg-white border border-amber-200 rounded-full text-slate-700 font-medium">{{ $g->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between gap-3 text-[11px] text-slate-500">
                    <span>
                        <i class="fa-solid fa-clock text-[#19A389] mr-1"></i>
                        Diperbarui <span id="jam_perbarui" class="font-semibold text-slate-700">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') }}</span> WIB
                    </span>
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" id="auto_refresh" class="accent-[#19A389]">
                        <span>Perbarui otomatis tiap 30 detik</span>
                    </label>
                </div>
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
                                <th class="p-3.5">Lokasi</th>
                                <th class="p-3.5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($absensis as $index => $absen)
                                <tr class="hover:bg-teal-50/40 transition">
                                    <td class="p-3.5 text-center font-semibold text-slate-500">{{ $absensis->firstItem() + $index }}</td>
                                    <td class="p-3.5 font-medium text-slate-700">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="p-3.5 font-bold text-slate-800">{{ $absen->nama_guru }}</td>
                                    <td class="p-3.5 text-[#138A73] font-semibold">{{ $nuptkTampil($absen) }}</td>
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
                                    <td class="p-3.5 whitespace-nowrap">
                                        @if($absen->lat_masuk !== null)
                                            <a href="https://www.google.com/maps?q={{ $absen->lat_masuk }},{{ $absen->lng_masuk }}" target="_blank" rel="noopener"
                                               class="text-[#138A73] hover:underline" title="Lihat lokasi absen masuk di peta">
                                                <i class="fa-solid fa-location-dot"></i> Masuk {{ $absen->jarak_masuk !== null ? $absen->jarak_masuk . ' m' : '' }}
                                            </a>
                                        @endif
                                        @if($absen->lat_keluar !== null)
                                            <a href="https://www.google.com/maps?q={{ $absen->lat_keluar }},{{ $absen->lng_keluar }}" target="_blank" rel="noopener"
                                               class="text-blue-600 hover:underline block" title="Lihat lokasi absen pulang di peta">
                                                <i class="fa-solid fa-location-dot"></i> Pulang {{ $absen->jarak_keluar !== null ? $absen->jarak_keluar . ' m' : '' }}
                                            </a>
                                        @endif
                                        @if($absen->lat_masuk === null && $absen->lat_keluar === null)
                                            <span class="text-slate-300">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">{{ $absen->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-6 text-center text-slate-400 italic">Belum ada data presensi tersimpan pada filter ini.</td>
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

    <script>
        // Penyegaran otomatis: halaman dimuat ulang tiap 30 detik agar presensi guru yang baru
        // masuk langsung tampil. Tidak memuat ulang saat admin sedang mengetik di kolom filter.
        (function () {
            var kotak = document.getElementById('auto_refresh');
            var KUNCI = 'rekapGuruAutoRefresh';
            var timer = null;

            function simpan(nilai) {
                try { localStorage.setItem(KUNCI, nilai ? '1' : '0'); } catch (e) {}
            }
            function baca() {
                try { return localStorage.getItem(KUNCI) === '1'; } catch (e) { return false; }
            }
            function mulai() {
                timer = setInterval(function () {
                    var aktif = document.activeElement;
                    var sedangMengetik = aktif && (aktif.tagName === 'INPUT' || aktif.tagName === 'SELECT' || aktif.tagName === 'TEXTAREA') && aktif !== kotak;
                    if (!sedangMengetik) { window.location.reload(); }
                }, 30000);
            }
            function berhenti() {
                if (timer) { clearInterval(timer); timer = null; }
            }

            kotak.checked = baca();
            if (kotak.checked) { mulai(); }

            kotak.addEventListener('change', function () {
                simpan(kotak.checked);
                if (kotak.checked) { mulai(); } else { berhenti(); }
            });
        })();
    </script>
</body>
</html>