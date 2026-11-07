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
</head>
<body class="bg-[#EAF0F6] font-sans antialiased overflow-x-hidden">
@php
    $fmtPersen = fn ($p) => $p === null ? '-' : rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . '%';
    $adaFilter = $terpilih !== null;

    $bulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $hurufHari   = ['Sn','Sl','Rb','Km','Jm','Sb','Mg']; // Sen Sel Rab Kam Jum Sab Min

    // Gaya sel ala Excel: huruf saja dengan warna latar lembut
    $gayaHuruf = [
        'H' => 'bg-emerald-100 text-emerald-700',
        'I' => 'bg-sky-100 text-sky-700',
        'S' => 'bg-amber-100 text-amber-700',
        'A' => 'bg-rose-100 text-rose-700',
    ];

    $bulanGrup = $kolom->groupBy(fn ($k) => $k->carbon->format('Y-m'));
    $kolomTetap = $semua ? 4 : 3;

    // Detail tiap tanggal untuk modal (mode satu kelas)
    $detailKolom = $semua ? [] : $kolom->mapWithKeys(fn ($k) => [$k->tanggal => [
        'tampil'     => Absen::namaHari($k->carbon) . ', ' . $k->carbon->format('d/m/Y'),
        'tglPendek'  => $k->carbon->format('d/m/Y'),
        'state'      => $k->state,
        'keterangan' => $k->keterangan,
        'urlSusulan' => ($k->susulan && $k->susulan['boleh'])
            ? route('guru.absen-siswa.susulan', [$k->susulan['jadwal_id'], $k->tanggal, 'kelas_mapel' => $terpilih['key'], 'periode' => $periodeId, 'dari' => $dari, 'sampai' => $sampai, 'mode' => $mode, 'bulan' => $bulan, 'tgl' => $tanggalPilih])
            : null,
        'penanggung' => $k->susulan ? ['nama' => $k->susulan['nama'], 'pengganti' => $k->susulan['pengganti']] : null,
    ]])->all();
@endphp

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">REKAP ABSENSI SISWA</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-5">

                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">Rekap Absensi Siswa</h2>
                        @if($adaFilter)
                            <p class="text-sm font-bold text-slate-800 mt-1">
                                @if($semua)
                                    Seluruh Kelas yang Diajar <span class="text-slate-400 mx-1">|</span>
                                @else
                                    Kelas {{ $terpilih['kelas'] }} <span class="text-slate-400 mx-1">|</span>
                                    Mata Pelajaran: {{ $terpilih['mapel'] }} <span class="text-slate-400 mx-1">|</span>
                                @endif
                                Guru Pengampu: {{ $guru->nama ?? Auth::user()->name }}
                            </p>
                        @endif
                    </div>

                </div>

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                    <!-- ============ FILTER ============ -->
                    <form method="GET" action="{{ route('guru.rekap-siswa') }}" id="formFilter"
                          class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end text-xs">

                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Kelas &amp; Mata Pelajaran</label>
                            <select name="kelas_mapel" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
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
                        </div>

                        @include('layouts.partials.filter-waktu')

                        <div class="md:col-span-2">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-filter"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>

                    @if(! $adaFilter)
                        <div class="p-8 text-center text-slate-400 text-xs italic border border-dashed border-slate-200 rounded-2xl">
                            Anda belum memiliki jadwal mengajar, sehingga belum ada rekap yang dapat ditampilkan.
                            Hubungi admin untuk mengatur jadwal.
                        </div>
                    @else

                        <hr class="border-slate-100">

                        <!-- ============ PERINGATAN BELUM DIABSEN ============ -->
                        @if($belumCount > 0)
                            <div class="p-3 bg-amber-100 border border-amber-200 text-amber-900 text-xs font-semibold rounded-xl flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base mt-0.5"></i>
                                <span>Perhatian: Terdapat {{ $belumCount }} jadwal pertemuan yang belum diabsen (kolom kuning, tanda "?").
                                    @if($semua)
                                        Untuk absen susulan, pilih satu kelas pada filter lalu klik tanggal yang berwarna kuning.
                                    @else
                                        Klik tanggal berwarna kuning untuk absen susulan. Hanya guru yang bersangkutan (guru pengganti bila ada) yang dapat mengisi.
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- ============ INFORMASI TANGGAL MERAH ============ -->
                        <div class="text-xs text-slate-700 leading-relaxed">
                            <span class="font-bold">📌 Informasi Periode Ini:</span>
                            @if($liburRentang->isNotEmpty())
                                Ada {{ $liburRentang->count() }} hari libur nasional/sekolah pada rentang ini
                                ({{ $liburRentang->map(fn ($l) => Absen::namaHari($l->tanggal) . ', ' . $l->tanggal->format('d/m/Y') . ' - ' . $l->keterangan)->implode('; ') }}).
                                Hari libur tidak dihitung sebagai hari efektif belajar.
                            @else
                                Tidak ada hari libur nasional pada rentang ini.
                            @endif
                            @unless($semua)
                                Hari efektif: <b>{{ $efektif }}</b> pertemuan.
                            @endunless
                        </div>

                        <!-- ============ MATRIKS REKAP (ringkas ala Excel) ============ -->
                        <div class="overflow-x-auto rounded-xl border border-slate-400">
                            <table class="min-w-full text-center text-[11px] border-separate border-spacing-0">
                                <thead class="font-bold text-white">
                                    {{-- Baris 1: nama bulan (digabung) --}}
                                    <tr class="bg-[#0f7a63]">
                                        <th rowspan="3" class="sticky left-0 z-20 bg-[#0f7a63] px-1 w-9 min-w-[2.25rem] border-b border-r border-slate-400">No</th>
                                        <th rowspan="3" class="sticky left-9 z-20 bg-[#0f7a63] px-2 text-left min-w-[8.5rem] border-b border-r border-slate-400">Nama Siswa</th>
                                        <th rowspan="3" class="px-2 text-left border-b border-r border-slate-400">NISN</th>
                                        @if($semua)
                                            <th rowspan="3" class="px-2 text-left border-b border-r border-slate-400">Kelas</th>
                                        @endif

                                        @foreach($bulanGrup as $ym => $grup)
                                            @php $first = $grup->first()->carbon; @endphp
                                            <th colspan="{{ $grup->count() }}" class="px-1 py-1 border-b border-r border-slate-400">
                                                {{ $bulanPendek[$first->month - 1] }} {{ $first->year }}
                                            </th>
                                        @endforeach

                                        <th colspan="4" class="px-1 py-1 border-b border-r border-slate-400">Total</th>
                                        <th rowspan="3" class="px-2 border-b border-slate-400">%<br>Hadir</th>
                                    </tr>

                                    {{-- Baris 2: tanggal (klik di mode satu kelas) + H I S A --}}
                                    <tr class="bg-[#0f7a63]">
                                        @foreach($kolom as $k)
                                            @php
                                                $bgHead = match($k->state) {
                                                    'belum' => 'bg-yellow-300 text-slate-900',
                                                    'libur' => 'bg-rose-400 text-white',
                                                    default => 'bg-[#0f7a63] text-white',
                                                };
                                            @endphp
                                            <th class="p-0 w-7 min-w-[1.75rem] border-b border-r border-slate-400 {{ $bgHead }}"
                                                title="{{ $k->carbon->format('d/m/Y') }}{{ $k->state === 'libur' ? ' - Libur: ' . $k->keterangan : '' }}{{ $k->state === 'belum' ? ' - Belum diabsen' : '' }}">
                                                @if($semua)
                                                    <div class="py-1">{{ $k->carbon->format('d') }}</div>
                                                @else
                                                    <button type="button" class="btn-kolom w-full py-1 hover:brightness-90" data-tanggal="{{ $k->tanggal }}">{{ $k->carbon->format('d') }}</button>
                                                @endif
                                            </th>
                                        @endforeach

                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">H</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">I</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">S</th>
                                        <th rowspan="2" class="w-7 px-1 border-b border-r border-slate-400">A</th>
                                    </tr>

                                    {{-- Baris 3: inisial hari --}}
                                    <tr class="bg-[#0f7a63]">
                                        @foreach($kolom as $k)
                                            @php
                                                $bgHead = match($k->state) {
                                                    'belum' => 'bg-yellow-300 text-slate-700',
                                                    'libur' => 'bg-rose-400 text-white',
                                                    default => 'bg-[#0f7a63] text-emerald-100',
                                                };
                                            @endphp
                                            <th class="p-0 py-0.5 text-[9px] font-semibold border-b border-r border-slate-400 {{ $bgHead }}">{{ $hurufHari[$k->carbon->dayOfWeekIso - 1] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody class="text-slate-700">
                                    @forelse($rekap as $i => $r)
                                        <tr class="group">
                                            <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 px-1 py-1 font-semibold border-b border-r border-slate-300">{{ $i + 1 }}</td>
                                            <td class="sticky left-9 z-10 bg-white group-hover:bg-slate-50 px-2 py-1 text-left font-bold text-slate-800 whitespace-nowrap border-b border-r border-slate-300">{{ $r->nama }}</td>
                                            <td class="px-2 py-1 text-left text-slate-500 border-b border-r border-slate-300">{{ $r->nisn ?? '-' }}</td>
                                            @if($semua)
                                                <td class="px-2 py-1 text-left whitespace-nowrap border-b border-r border-slate-300">
                                                    <span class="font-semibold">{{ $r->kelas }}</span>
                                                    @if($mapelBanyak)<span class="block text-[9px] text-slate-400">{{ $r->mapel }}</span>@endif
                                                </td>
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
                                        <tr>
                                            <td colspan="{{ $kolomTetap + $kolom->count() + 5 }}" class="p-6 text-slate-400 italic">
                                                Belum ada siswa terdaftar di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($kolom->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada pertemuan yang tercatat atau terjadwal sampai hari ini pada rentang tanggal ini.</p>
                        @endif

                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            <b>H</b> Hadir, <b>I</b> Izin, <b>S</b> Sakit, <b>A</b> Alpa, <b>?</b> belum diabsen, <b>L</b> libur, <b>-</b> tidak ada pertemuan.
                            Inisial hari: Sn Senin, Sl Selasa, Rb Rabu, Km Kamis, Jm Jumat, Sb Sabtu, Mg Minggu.
                            % Hadir = Hadir &divide; hari efektif (pertemuan yang sudah diabsen) &times; 100%; libur dan belum diabsen tidak dihitung.
                            @unless($semua)
                                Klik tanggal untuk hapus absen harian (seluruh siswa) atau absen susulan. Nama guru pengabsen ada di kartu kelas pada Dashboard.
                            @endunless
                        </p>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @if($adaFilter && ! $semua)
        <!-- MODAL DETAIL TANGGAL (tanggal terkunci / read-only) -->
        <div id="modalTanggal" class="hidden fixed inset-0 z-[60] bg-slate-900/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl p-6 shadow-2xl w-full max-w-md">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-base font-extrabold text-slate-900 uppercase">Detail Pertemuan</h3>
                    <button type="button" id="mtTutup" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Kelas &amp; Mapel</label>
                        <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800">Kelas {{ $terpilih['kelas'] }} - {{ $terpilih['mapel'] }}</div>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Tanggal</label>
                        <div class="p-2.5 bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-800 flex justify-between items-center">
                            <span id="mtTanggal"></span><i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                    </div>
                    <div id="mtStatus" class="p-3 rounded-xl font-semibold"></div>
                    <div id="mtAksi" class="flex flex-wrap gap-2 pt-1"></div>
                </div>
            </div>
        </div>

        <!-- Form hapus per hari (seluruh siswa) -->
        <form id="formHapusHari" method="POST" action="{{ route('guru.rekap-siswa.hapus-hari') }}" class="hidden">
            @csrf
            @method('DELETE')
            <input type="hidden" name="kelas" value="{{ $terpilih['kelas'] }}">
            <input type="hidden" name="mapel" value="{{ $terpilih['mapel'] }}">
            <input type="hidden" name="tanggal" id="hapusTanggal">
            <input type="hidden" name="kelas_mapel" value="{{ $terpilih['key'] }}">
            <input type="hidden" name="periode" value="{{ $periodeId }}">
            <input type="hidden" name="dari" value="{{ $dari }}">
            <input type="hidden" name="sampai" value="{{ $sampai }}">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tanggal_filter" value="{{ $tanggalPilih }}">
        </form>
    @endif

    <div id="dataDetail" class="hidden" data-detail="{{ json_encode($detailKolom) }}"></div>

    @if(session('success') || session('error'))
        <div id="flashData" class="hidden"
             data-tipe="{{ session('success') ? 'success' : 'error' }}"
             data-pesan="{{ session('success') ?? session('error') }}"></div>
    @endif

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        // ===== MODAL DETAIL TANGGAL (mode satu kelas) =====
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
                                ? 'Terkunci: hanya Guru Pengganti (' + d.penanggung.nama + ') yang dapat mengabsen tanggal ini. Guru utama diblokir.'
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
        }

        // Hapus absen SATU HARI (seluruh siswa)
        function hapusHari(tanggalIso, tanggalTampil) {
            Swal.fire({
                title: 'Hapus absen tanggal ' + tanggalTampil + '?',
                html: 'Absen <strong>seluruh siswa</strong> pada tanggal ini akan dihapus permanen. '
                    + 'Penghapusan per siswa tidak tersedia. Setelah itu Anda dapat absen ulang dari dashboard.',
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
