@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Utama - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans antialiased overflow-x-hidden">
@php
    $fmtPersen   = fn ($p) => $p === null ? '-' : rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . '%';
    $bulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $hurufHari   = ['Sn','Sl','Rb','Km','Jm','Sb','Mg'];
    $gayaHuruf   = [
        'H' => 'bg-emerald-100 text-emerald-700',
        'I' => 'bg-sky-100 text-sky-700',
        'S' => 'bg-amber-100 text-amber-700',
        'A' => 'bg-rose-100 text-rose-700',
    ];
    $kolom     = $hasil['kolom'] ?? collect();
    $bulanGrup = $kolom->groupBy(fn ($k) => $k->carbon->format('Y-m'));
    $klikable  = $bisaSusulan && ! $semua;

    // Detail tanggal untuk modal absen susulan (guru, mode satu kelas)
    $detailKolom = $klikable ? $kolom->mapWithKeys(fn ($k) => [$k->tanggal => [
        'tampil'     => Absen::namaHari($k->carbon) . ', ' . $k->carbon->format('d/m/Y'),
        'state'      => $k->state,
        'oleh'       => $k->oleh,
        'keterangan' => $k->keterangan,
        'urlSusulan' => ($k->susulan && $k->susulan['boleh'])
            ? route('guru.absen-utama.susulan', [$k->susulan['jadwal_id'], $k->tanggal, 'kelas' => $kelas, 'periode' => $periodeId, 'dari' => $dari, 'sampai' => $sampai])
            : null,
        'penanggung' => $k->susulan ? ['nama' => $k->susulan['nama'], 'pengganti' => $k->susulan['pengganti']] : null,
    ]])->all() : [];
@endphp

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="px-4 sm:px-8 pt-4 pb-2 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">REKAP ABSENSI UTAMA</h1>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-5">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">Rekap Absensi Utama (Sekolah)</h2>
                    <p class="text-sm font-bold text-slate-800 mt-1">
                        Absensi harian jam ke-1, sekali per kelas per hari
                        @if($kelas)<span class="text-slate-400 mx-1">|</span> {{ $semua ? 'Seluruh Kelas' : 'Kelas ' . $kelas }}@endif
                    </p>
                </div>

                <!-- STATUS HARI INI (semua kelas): bantu guru mengingatkan guru jam ke-1 -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-bell text-amber-500"></i> Status Absensi Utama Hari Ini</h3>
                        <p class="text-[11px] text-slate-500">Dapat dilihat semua guru &amp; admin. Kelas yang belum diisi: mohon ingatkan guru jam ke-1. Data per siswa juga tampil di kolom UTAMA saat absen mata pelajaran.</p>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#0f7a63] text-white font-bold">
                                <tr><th class="p-2.5">Kelas</th><th class="p-2.5">Jam ke-1</th><th class="p-2.5">Guru Jam ke-1</th><th class="p-2.5">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($ringkasanHariIni as $rh)
                                    <tr>
                                        <td class="p-2.5 font-bold">Kelas {{ $rh->kelas }}</td>
                                        <td class="p-2.5">{{ $rh->jam_masuk }}</td>
                                        <td class="p-2.5">{{ $rh->guru ?: '-' }}@if($rh->is_pengganti) <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 text-[10px] font-bold">Pengganti</span>@endif</td>
                                        <td class="p-2.5">
                                            @if($rh->status === 'sudah')
                                                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]"><i class="fa-solid fa-lock mr-1"></i>Selesai oleh {{ $rh->oleh }}</span>
                                            @elseif($rh->status === 'belum')
                                                <span class="px-2 py-1 rounded-full bg-yellow-200 text-yellow-900 font-bold text-[10px]"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Belum diisi - ingatkan {{ $rh->guru }}</span>
                                            @elseif($rh->status === 'libur')
                                                <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">Libur</span>
                                            @else
                                                <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Belum waktunya</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-slate-400 italic">Tidak ada jadwal pelajaran hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                    <form method="GET" action="{{ $aksi }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end text-xs">
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Kelas</label>
                            <select name="kelas" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:border-[#19A389]">
                                @if($kelasList->isNotEmpty())
                                    <option value="ALL" {{ $semua ? 'selected' : '' }}>Semua Kelas</option>
                                @endif
                                @forelse($kelasList as $k)
                                    <option value="{{ $k }}" {{ ! $semua && $kelas === $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                                @empty
                                    <option value="">Belum ada kelas</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Rentang Pembelajaran</label>
                            <select name="periode" id="selectPeriode" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 focus:outline-none focus:border-[#19A389]">
                                <option value="">- Atur tanggal manual -</option>
                                @foreach($periodeList as $p)
                                    <option value="{{ $p->id }}" data-mulai="{{ $p->tanggal_mulai->toDateString() }}" data-selesai="{{ $p->tanggal_selesai->toDateString() }}"
                                            {{ (string) $periodeId === (string) $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Rentang Tanggal</label>
                            <div class="flex items-center gap-2">
                                <input type="date" name="dari" id="inputDari" value="{{ $dari }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                                <span class="font-semibold text-slate-500">s.d.</span>
                                <input type="date" name="sampai" id="inputSampai" value="{{ $sampai }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow transition">
                                <i class="fa-solid fa-filter mr-1"></i> Terapkan
                            </button>
                        </div>
                    </form>

                    @if(! $hasil)
                        <div class="p-8 text-center text-slate-400 text-xs italic border border-dashed border-slate-200 rounded-2xl">Belum ada kelas yang dapat ditampilkan.</div>
                    @else
                        <hr class="border-slate-100">

                        @if($hasil['belumCount'] > 0)
                            <div class="p-3 bg-amber-100 border border-amber-200 text-amber-900 text-xs font-semibold rounded-xl flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                                <span>{{ $hasil['belumCount'] }} hari sekolah belum memiliki Absensi Utama (kolom kuning, tanda "?").
                                    @if($klikable) Klik tanggal kuning untuk absen susulan (hanya guru jam ke-1 / penggantinya). @elseif($semua && $bisaSusulan) Pilih satu kelas pada filter untuk absen susulan. @endif</span>
                            </div>
                        @endif

                        <div class="text-xs text-slate-700 leading-relaxed">
                            <span class="font-bold">📌 Informasi Periode Ini:</span>
                            @if($hasil['liburRentang']->isNotEmpty())
                                Ada {{ $hasil['liburRentang']->count() }} hari libur
                                ({{ $hasil['liburRentang']->map(fn ($l) => Absen::namaHari($l->tanggal) . ', ' . $l->tanggal->format('d/m/Y') . ' - ' . $l->keterangan)->implode('; ') }}),
                                tidak dihitung sebagai hari efektif.
                            @else
                                Tidak ada hari libur pada rentang ini.
                            @endif
                            Hari efektif: <b>{{ $hasil['efektif'] }}</b>.
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-400">
                            <table class="min-w-full text-center text-[11px] border-separate border-spacing-0">
                                <thead class="font-bold text-white">
                                    <tr class="bg-[#0f7a63]">
                                        <th rowspan="3" class="sticky left-0 z-20 bg-[#0f7a63] px-1 w-9 min-w-[2.25rem] border-b border-r border-slate-400">No</th>
                                        <th rowspan="3" class="sticky left-9 z-20 bg-[#0f7a63] px-2 text-left min-w-[8.5rem] border-b border-r border-slate-400">Nama Siswa</th>
                                        <th rowspan="3" class="px-2 text-left border-b border-r border-slate-400">NISN</th>
                                        @if($semua)
                                            <th rowspan="3" class="px-2 text-left border-b border-r border-slate-400">Kelas</th>
                                        @endif
                                        @foreach($bulanGrup as $grup)
                                            @php $first = $grup->first()->carbon; @endphp
                                            <th colspan="{{ $grup->count() }}" class="px-1 py-1 border-b border-r border-slate-400">{{ $bulanPendek[$first->month - 1] }} {{ $first->year }}</th>
                                        @endforeach
                                        <th colspan="4" class="px-1 py-1 border-b border-r border-slate-400">Total</th>
                                        <th rowspan="3" class="px-2 border-b border-slate-400">%<br>Hadir</th>
                                    </tr>
                                    <tr class="bg-[#0f7a63]">
                                        @foreach($kolom as $k)
                                            @php
                                                $bg = match($k->state) { 'belum' => 'bg-yellow-300 text-slate-900', 'libur' => 'bg-rose-400 text-white', default => 'bg-[#0f7a63] text-white' };
                                            @endphp
                                            <th class="p-0 w-7 min-w-[1.75rem] border-b border-r border-slate-400 {{ $bg }}"
                                                title="{{ $k->carbon->format('d/m/Y') }}{{ $k->oleh ? ' - Diselesaikan oleh ' . $k->oleh : '' }}{{ $k->state === 'libur' ? ' - Libur: ' . $k->keterangan : '' }}{{ $k->state === 'belum' ? ' - Belum diabsen' : '' }}">
                                                @if($klikable && $k->state === 'belum')
                                                    <button type="button" class="btn-kolom w-full py-1 hover:brightness-90 underline" data-tanggal="{{ $k->tanggal }}">{{ $k->carbon->format('d') }}</button>
                                                @else
                                                    <div class="py-1">{{ $k->carbon->format('d') }}</div>
                                                @endif
                                            </th>
                                        @endforeach
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">H</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">I</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">S</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">A</th>
                                    </tr>
                                    <tr class="bg-[#0f7a63]">
                                        @foreach($kolom as $k)
                                            @php $bg = match($k->state) { 'belum' => 'bg-yellow-300 text-slate-700', 'libur' => 'bg-rose-400 text-white', default => 'bg-[#0f7a63] text-emerald-100' }; @endphp
                                            <th class="py-0.5 text-[9px] font-semibold border-b border-r border-slate-400 {{ $bg }}">{{ $hurufHari[$k->carbon->dayOfWeekIso - 1] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="text-slate-700">
                                    @forelse($hasil['rekap'] as $i => $r)
                                        <tr class="group">
                                            <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $i + 1 }}</td>
                                            <td class="sticky left-9 z-10 bg-white group-hover:bg-slate-50 px-2 py-1 text-left font-bold text-slate-800 whitespace-nowrap border-b border-r border-slate-300">{{ $r->nama }}</td>
                                            <td class="px-2 py-1 text-left text-slate-500 border-b border-r border-slate-300">{{ $r->nisn ?? '-' }}</td>
                                            @if($semua)
                                                <td class="px-2 py-1 text-left font-semibold whitespace-nowrap border-b border-r border-slate-300">{{ $r->kelas }}</td>
                                            @endif

                                            @foreach($kolom as $tgl => $k)
                                                @php
                                                    $stSel = $r->st[$tgl] ?? 'kosong';
                                                    $nilai = $r->sel[$tgl] ?? null;
                                                @endphp
                                                @if($stSel === 'libur')
                                                    <td class="p-0 bg-rose-300 text-rose-900 font-bold border-b border-r border-slate-300">L</td>
                                                @elseif($stSel === 'belum')
                                                    <td class="p-0 bg-yellow-100 text-yellow-600 font-bold border-b border-r border-slate-300">?</td>
                                                @elseif($stSel === 'sudah' && $nilai)
                                                    <td class="p-0 font-bold border-b border-r border-slate-300 {{ $gayaHuruf[$nilai] ?? '' }}">{{ $nilai }}</td>
                                                @else
                                                    <td class="p-0 text-slate-300 border-b border-r border-slate-300">-</td>
                                                @endif
                                            @endforeach

                                            <td class="px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $r->total['H'] }}</td>
                                            <td class="px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $r->total['I'] }}</td>
                                            <td class="px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $r->total['S'] }}</td>
                                            <td class="px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $r->total['A'] }}</td>
                                            <td class="px-2 py-1 font-bold border-b border-slate-300">{{ $fmtPersen($r->persen) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="{{ ($semua ? 10 : 9) + $kolom->count() }}" class="p-6 text-slate-400 italic">Belum ada siswa terdaftar di kelas ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($kolom->isEmpty())
                            <p class="text-xs text-slate-400 italic">Tidak ada hari sekolah pada rentang tanggal ini.</p>
                        @endif

                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            <b>H</b> Hadir, <b>I</b> Izin, <b>S</b> Sakit, <b>A</b> Alpa, <b>?</b> belum diabsen, <b>L</b> libur.
                            Arahkan kursor ke tanggal untuk melihat guru yang menyelesaikan Absensi Utama.
                            Rekap ini terpisah dari rekap mata pelajaran; % Hadir = Hadir &divide; hari sekolah yang sudah diabsen &times; 100%.
                        </p>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @if($klikable)
        <!-- MODAL ABSEN SUSULAN UTAMA (tanggal terkunci / read-only) -->
        <div id="modalTanggal" class="hidden fixed inset-0 z-[60] bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 shadow-2xl w-full max-w-md">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-base font-extrabold text-slate-900 uppercase">Absensi Utama Susulan</h3>
                    <button type="button" id="mtTutup" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Kelas</label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">Kelas {{ $kelas }}</div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Tanggal</label>
                        <div class="p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-800 flex justify-between items-center">
                            <span id="mtTanggal"></span><i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                    </div>
                    <div id="mtStatus" class="p-3 rounded-xl font-semibold bg-yellow-100 border border-yellow-300 text-yellow-900">Belum Diabsen.</div>
                    <div id="mtAksi" class="flex flex-wrap gap-2 pt-1"></div>
                </div>
            </div>
        </div>
    @endif

    <div id="dataDetail" class="hidden" data-detail="{{ json_encode($detailKolom) }}"></div>

    @if(session('success') || session('error'))
        <div id="flashData" class="hidden" data-tipe="{{ session('success') ? 'success' : 'error' }}" data-pesan="{{ session('success') ?? session('error') }}"></div>
    @endif

    <script>
        const DETAIL = JSON.parse(document.getElementById('dataDetail').dataset.detail || '{}');
        const modalTanggal = document.getElementById('modalTanggal');

        if (modalTanggal) {
            document.querySelectorAll('.btn-kolom').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const d = DETAIL[btn.dataset.tanggal];
                    if (!d) return;

                    document.getElementById('mtTanggal').textContent = d.tampil;
                    const aksi = document.getElementById('mtAksi');
                    aksi.replaceChildren();

                    if (d.urlSusulan) {
                        const a = document.createElement('a');
                        a.href = d.urlSusulan;
                        a.className = 'px-4 py-2.5 rounded-xl font-bold text-xs bg-[#19A389] hover:bg-[#138A73] text-white transition';
                        a.textContent = 'Lanjut Absen Susulan';
                        aksi.append(a);
                    } else if (d.penanggung) {
                        const info = document.createElement('div');
                        info.className = 'w-full p-3 rounded-xl bg-slate-800 text-white font-semibold';
                        info.textContent = d.penanggung.pengganti
                            ? 'Terkunci: hanya Guru Pengganti (' + d.penanggung.nama + ') yang dapat mengisi tanggal ini. Guru utama diblokir.'
                            : 'Terkunci: hanya guru jam ke-1 (' + d.penanggung.nama + ') yang dapat mengisi tanggal ini.';
                        aksi.append(info);
                    }

                    modalTanggal.classList.remove('hidden');
                });
            });

            document.getElementById('mtTutup').addEventListener('click', function () { modalTanggal.classList.add('hidden'); });
            modalTanggal.addEventListener('click', function (e) { if (e.target === modalTanggal) modalTanggal.classList.add('hidden'); });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        const flash = document.getElementById('flashData');
        if (flash) {
            const bar = document.createElement('div');
            bar.className = 'fixed top-5 right-5 z-[100] max-w-sm p-4 rounded-2xl shadow-xl border-l-4 bg-white text-xs '
                + (flash.dataset.tipe === 'success' ? 'border-[#19A389]' : 'border-rose-500');
            bar.textContent = flash.dataset.pesan;
            document.body.appendChild(bar);
            setTimeout(function () { bar.remove(); }, 7000);
        }

        const selectPeriode = document.getElementById('selectPeriode');
        if (selectPeriode) {
            selectPeriode.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.dataset.mulai) {
                    document.getElementById('inputDari').value = opt.dataset.mulai;
                    document.getElementById('inputSampai').value = opt.dataset.selesai;
                }
            });
        }
    </script>
</body>
</html>
