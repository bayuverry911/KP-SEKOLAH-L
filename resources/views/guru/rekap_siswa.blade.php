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
    $warnaHuruf = [
        'H' => 'text-green-600',
        'I' => 'text-amber-500',
        'S' => 'text-red-600',
        'A' => 'text-fuchsia-700',
    ];
    $fmtPersen = fn ($p) => $p === null ? '-' : rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . '%';
    $adaFilter = $terpilih !== null;
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

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                    <!-- ============ FILTER ============ -->
                    <form method="GET" action="{{ route('guru.rekap-siswa') }}" id="formFilter"
                          class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end text-xs">

                        <div class="md:col-span-3">
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

                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Rentang Pembelajaran</label>
                            <select name="periode" id="selectPeriode" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                                <option value="">- Atur tanggal manual -</option>
                                @foreach($periodeList as $p)
                                    <option value="{{ $p->id }}"
                                            data-mulai="{{ $p->tanggal_mulai->toDateString() }}"
                                            data-selesai="{{ $p->tanggal_selesai->toDateString() }}"
                                            {{ (string) $periodeId === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Rentang Tanggal</label>
                            <div class="flex items-center gap-2">
                                <input type="date" name="dari" id="inputDari" value="{{ $dari }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
                                <span class="font-semibold text-slate-500">s.d.</span>
                                <input type="date" name="sampai" id="inputSampai" value="{{ $sampai }}"
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
                            </div>
                        </div>

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
                            <div class="p-4 bg-amber-100 border border-amber-200 text-amber-900 text-xs sm:text-sm font-semibold rounded-2xl flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg mt-0.5"></i>
                                <span>Perhatian: Terdapat {{ $belumCount }} Jadwal Pertemuan yang Belum Diabsen (Kolom Kuning).
                                    @if($semua)
                                        Pilih satu kelas pada filter untuk melihat guru pengabsen dan melakukan absen susulan.
                                    @else
                                        Absen susulan hanya dapat diisi oleh guru yang bersangkutan (guru pengganti bila ada) agar kalkulasi % Kehadiran akurat.
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
                                Hari efektif pada rentang ini: <b>{{ $efektif }}</b> pertemuan.
                            @endunless
                        </div>

                        @php
                            $bulanPendek = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            $hariPendek  = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                            $chip = function ($h) {
                                $w = match($h) {
                                    'H' => 'bg-emerald-500 text-white',
                                    'I' => 'bg-sky-500 text-white',
                                    'S' => 'bg-amber-400 text-slate-900',
                                    'A' => 'bg-rose-500 text-white',
                                    default => 'bg-slate-200 text-slate-500',
                                };
                                return '<span class="inline-flex w-6 h-6 items-center justify-center rounded-md text-[11px] font-bold ' . $w . '">' . e($h) . '</span>';
                            };
                            $kolomTetap = $semua ? 4 : 3;
                        @endphp

                        <!-- ============ MATRIKS REKAP ============ -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="w-full text-center text-xs border-collapse">
                                <thead class="font-bold">
                                    <tr class="bg-[#0f7a63] text-white">
                                        <th rowspan="2" class="p-3 w-10">No</th>
                                        <th rowspan="2" class="p-3 text-left">NISN</th>
                                        <th rowspan="2" class="p-3 text-left min-w-[10rem]">Nama Siswa</th>
                                        @if($semua)
                                            <th rowspan="2" class="p-3 text-left">Kelas</th>
                                        @endif

                                        @foreach($kolom as $k)
                                            @php
                                                $hp = $hariPendek[$k->carbon->dayOfWeekIso - 1];
                                                $bp = $bulanPendek[$k->carbon->month - 1];
                                            @endphp

                                            @if($semua)
                                                {{-- Mode semua kelas: header ringkas, tanpa info pengabsen --}}
                                                @php
                                                    $bgHead = match($k->state) {
                                                        'belum' => 'bg-yellow-300 text-slate-900',
                                                        'libur' => 'bg-rose-400 text-white',
                                                        default => 'bg-[#0f7a63] text-white',
                                                    };
                                                @endphp
                                                <th rowspan="2" class="p-1.5 min-w-[2.8rem] {{ $bgHead }} align-top"
                                                    title="{{ $k->state === 'libur' ? 'Libur: ' . $k->keterangan : ($k->state === 'belum' ? 'Ada kelas yang belum diabsen' : '') }}">
                                                    <div class="text-[12px] leading-tight">{{ $k->carbon->format('d') }}</div>
                                                    <div class="text-[9px] font-semibold leading-tight">{{ $bp }}</div>
                                                    <div class="text-[9px] font-medium leading-tight">({{ $hp }})</div>
                                                </th>

                                            @elseif($k->state === 'sudah')
                                                <th rowspan="2" class="p-2 min-w-[9.5rem] bg-teal-100 text-slate-800 align-top">
                                                    <div class="bg-[#0f7a63] text-white rounded-lg p-2 leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-600 mt-2">Diabsen oleh:</div>
                                                    <div class="text-[11px] font-bold">{{ $k->guru }}{{ $k->pengganti ? ' (Ganti)' : '' }}</div>
                                                    <button type="button"
                                                            data-tanggal="{{ $k->tanggal }}"
                                                            data-tampil="{{ $k->carbon->format('d/m/Y') }}"
                                                            class="btn-hapus-hari mt-1.5 px-2 py-1 text-rose-700 hover:bg-rose-100 rounded-md text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-trash"></i> Hapus Harian
                                                    </button>
                                                </th>

                                            @elseif($k->state === 'belum')
                                                <th rowspan="2" class="p-2 min-w-[9rem] bg-yellow-300 text-slate-900 align-top">
                                                    <div class="leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                    <div class="mt-2 text-rose-700 font-extrabold uppercase leading-tight">
                                                        <i class="fa-solid fa-triangle-exclamation"></i> Belum<br>Diabsen
                                                    </div>
                                                    @if($k->susulan && $k->susulan['boleh'])
                                                        <a href="{{ route('guru.absen-siswa.susulan', [$k->susulan['jadwal_id'], $k->tanggal, 'kelas_mapel' => $terpilih['key'], 'periode' => $periodeId, 'dari' => $dari, 'sampai' => $sampai]) }}"
                                                           class="mt-2 inline-flex items-center gap-1 px-2.5 py-1.5 bg-[#19A389] hover:bg-[#138A73] text-white rounded-lg text-[10px] font-bold normal-case">
                                                            <i class="fa-solid fa-pen-to-square"></i> Absen Susulan
                                                        </a>
                                                    @elseif($k->susulan)
                                                        <div class="mt-2 inline-flex items-start gap-1 px-2 py-1 bg-slate-800 text-white rounded-lg text-[10px] font-semibold normal-case leading-tight">
                                                            <i class="fa-solid fa-lock mt-0.5"></i>
                                                            <span>Hanya {{ $k->susulan['pengganti'] ? 'Guru Pengganti' : 'Guru' }}<br>({{ $k->susulan['nama'] }})</span>
                                                        </div>
                                                    @endif
                                                </th>

                                            @elseif($k->state === 'libur')
                                                <th rowspan="2" class="p-2 min-w-[8rem] bg-rose-400 text-white align-top">
                                                    <div class="leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                    <div class="mt-3 leading-tight">LIBUR{{ $k->keterangan ? ' (' . $k->keterangan . ')' : '' }}</div>
                                                </th>

                                            @else
                                                <th rowspan="2" class="p-2 min-w-[8rem] bg-[#0f7a63] text-white align-top">
                                                    <div class="leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                    <div class="mt-3 text-[11px] font-semibold text-emerald-100">({{ $k->state === 'hariini' ? 'Hari Ini' : 'Masa Depan' }})</div>
                                                </th>
                                            @endif
                                        @endforeach

                                        <th colspan="4" class="p-2 border-l border-white/20">Total</th>
                                        <th rowspan="2" class="p-3 border-l border-white/20">% Kehadiran</th>
                                    </tr>
                                    <tr class="bg-[#0f7a63] text-white">
                                        <th class="p-2 border-l border-white/20">H</th>
                                        <th class="p-2">I</th>
                                        <th class="p-2">S</th>
                                        <th class="p-2">A</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($rekap as $i => $r)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="p-2.5 font-semibold">{{ $i + 1 }}</td>
                                            <td class="p-2.5 text-left text-slate-500">{{ $r->nisn ?? '-' }}</td>
                                            <td class="p-2.5 text-left font-bold text-slate-800 whitespace-nowrap">{{ $r->nama }}</td>
                                            @if($semua)
                                                <td class="p-2.5 text-left whitespace-nowrap">
                                                    <span class="font-semibold">{{ $r->kelas }}</span>
                                                    @if($mapelBanyak)<span class="block text-[10px] text-slate-400">{{ $r->mapel }}</span>@endif
                                                </td>
                                            @endif

                                            @foreach($kolom as $tgl => $k)
                                                @php
                                                    $stSel = $r->st[$tgl] ?? 'kosong';
                                                    $nilai = $r->sel[$tgl] ?? null;
                                                @endphp

                                                @if($stSel === 'libur')
                                                    <td class="p-1.5 bg-rose-300 font-bold text-rose-900 text-[11px]">{{ $semua ? 'L' : 'LIBUR' }}</td>
                                                @elseif($stSel === 'belum')
                                                    <td class="p-1.5 bg-yellow-100 text-slate-400 font-bold">?</td>
                                                @elseif($stSel === 'sudah')
                                                    <td class="p-1.5">{!! $nilai ? $chip($nilai) : '<span class="text-slate-300">-</span>' !!}</td>
                                                @else
                                                    <td class="p-1.5 text-slate-300 font-bold">-</td>
                                                @endif
                                            @endforeach

                                            <td class="p-2.5 border-l border-slate-100 font-semibold">{{ $r->total['H'] }}</td>
                                            <td class="p-2.5 font-semibold">{{ $r->total['I'] }}</td>
                                            <td class="p-2.5 font-semibold">{{ $r->total['S'] }}</td>
                                            <td class="p-2.5 font-semibold">{{ $r->total['A'] }}</td>
                                            <td class="p-2.5 border-l border-slate-100 font-bold">{{ $fmtPersen($r->persen) }}</td>
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
                            <p class="text-xs text-slate-400 italic">Tidak ada jadwal pertemuan atau absen pada rentang tanggal ini.</p>
                        @endif

                        @if($semua)
                            <p class="text-[11px] text-slate-600">
                                Mode semua kelas hanya menampilkan rekap. "-" = kelas tidak ada pertemuan pada tanggal itu, "?" = belum diabsen, "L" = libur.
                                Untuk melihat guru pengabsen, menghapus absen harian, atau absen susulan, pilih <b>satu kelas</b> pada filter.
                                % = Hadir &divide; hari efektif kelas siswa tersebut (yang sudah diabsen) &times; 100%.
                            </p>
                        @else
                            <p class="text-[11px] text-slate-600">
                                *Untuk menghapus kesalahan input, gunakan tombol 'Hapus Harian' di bagian atas kolom tanggal bersangkutan (berlaku untuk seluruh siswa pada tanggal itu).
                                Hari libur &amp; hari belum diabsen tidak dimasukkan dalam pembagi % Kehadiran siswa: % = Hadir &divide; hari efektif (yang sudah diabsen) &times; 100%.
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </main>
    </div>

    @if($adaFilter && ! $semua)
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
        </form>
    @endif

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

        // Pilih periode pembelajaran -> isi otomatis rentang tanggal
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

        document.querySelectorAll('.btn-hapus-hari').forEach(function (btn) {
            btn.addEventListener('click', function () { hapusHari(btn.dataset.tanggal, btn.dataset.tampil); });
        });

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
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus Harian',
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
