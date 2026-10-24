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

    <div id="mobileOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button id="btnToggleSidebar" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
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
                            Kelas {{ $terpilih['kelas'] }} <span class="text-slate-400 mx-1">|</span>
                            Mata Pelajaran: {{ $terpilih['mapel'] }} <span class="text-slate-400 mx-1">|</span>
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
                                @forelse($opsi as $o)
                                    <option value="{{ $o['key'] }}" {{ $adaFilter && $terpilih['key'] === $o['key'] ? 'selected' : '' }}>
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
                                <option value="">Atur tanggal manual - </option>
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

                        <!-- ============ INFORMASI TANGGAL MERAH ============ -->
                        <div class="text-xs text-slate-700 leading-relaxed">
                            <span class="font-bold">📌 Informasi Periode Ini:</span>
                            @if($liburRentang->isNotEmpty())
                                Tanggal Merah (
                                {{ $liburRentang->map(fn ($l) => $l->tanggal->day . ' ' . substr(Absen::BULAN[$l->tanggal->month], 0, 3) . ' ' . $l->tanggal->year . ': ' . $l->keterangan)->implode('; ') }}
                                ). Hari libur nasional/sekolah tidak dihitung sebagai hari efektif belajar.
                            @else
                                Tidak ada tanggal merah pada rentang ini.
                            @endif
                        </div>

                        <!-- ============ MATRIKS REKAP ============ -->
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="w-full text-center text-xs border-collapse">
                                <thead class="text-white font-bold">
                                    <tr class="bg-[#0f7a63]">
                                        <th rowspan="2" class="p-3 w-10">No</th>
                                        <th rowspan="2" class="p-3 text-left min-w-[10rem]">Nama Siswa</th>

                                        @foreach($kolom as $k)
                                            @if($k->libur)
                                                <th rowspan="2" class="p-3 min-w-[7rem] bg-rose-400">
                                                    <div class="leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                </th>
                                            @else
                                                <th rowspan="2" class="p-3 min-w-[9.5rem]">
                                                    <div class="leading-tight">{{ Absen::namaHari($k->carbon) }},<br>{{ $k->carbon->format('d/m/Y') }}</div>
                                                    <div class="text-[10px] font-semibold text-emerald-100 mt-1">
                                                        Guru: {{ $k->guru }}{{ $k->pengganti ? ' (Ganti)' : '' }}
                                                    </div>
                                                    <!-- PERBAIKAN: Menggunakan data-attribute -->
                                                    <button type="button"
                                                            data-tanggal="{{ $k->tanggal }}"
                                                            data-tampil="{{ $k->carbon->format('d/m/Y') }}"
                                                            class="btn-hapus-hari mt-1.5 px-2 py-1 bg-rose-600 hover:bg-rose-700 rounded-md text-[10px] font-bold inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-trash"></i> Hapus Sehari
                                                    </button>
                                                </th>
                                            @endif
                                        @endforeach

                                        <th colspan="4" class="p-2 border-l border-white/20">Total</th>
                                        <th rowspan="2" class="p-3 border-l border-white/20">% Kehadiran</th>
                                    </tr>
                                    <tr class="bg-[#0f7a63]">
                                        <th class="p-2 border-l border-white/20">H</th>
                                        <th class="p-2">I</th>
                                        <th class="p-2">S</th>
                                        <th class="p-2">A</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($rekap as $i => $r)
                                        <tr class="hover:bg-slate-50">
                                            <td class="p-3 font-semibold">{{ $i + 1 }}.</td>
                                            <td class="p-3 text-left font-bold text-slate-800">{{ $r->nama }}</td>

                                            @foreach($kolom as $tgl => $k)
                                                @php $nilai = $r->sel[$tgl] ?? null; @endphp
                                                @if($k->libur)
                                                    <td class="p-2 bg-rose-300/70 font-bold text-rose-800">
                                                        LIBUR
                                                        @if($loop->parent->first && $k->keterangan)
                                                            <br><span class="font-semibold">({{ $k->keterangan }})</span>
                                                        @endif
                                                    </td>
                                                @else
                                                    <td class="p-3 font-bold text-sm {{ $warnaHuruf[$nilai] ?? 'text-slate-300' }}">{{ $nilai ?? '-' }}</td>
                                                @endif
                                            @endforeach

                                            <td class="p-3 border-l border-slate-100 font-semibold">{{ $r->total['H'] }}</td>
                                            <td class="p-3 font-semibold">{{ $r->total['I'] }}</td>
                                            <td class="p-3 font-semibold">{{ $r->total['S'] }}</td>
                                            <td class="p-3 font-semibold">{{ $r->total['A'] }}</td>
                                            <td class="p-3 border-l border-slate-100 font-bold">{{ $fmtPersen($r->persen) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 7 + $kolom->count() }}" class="p-6 text-slate-400 italic">
                                                Belum ada siswa terdaftar di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($kolom->where('libur', false)->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada absen yang tercatat pada rentang tanggal ini.</p>
                        @endif

                        <p class="text-[11px] text-slate-600">
                            *Untuk menghapus kesalahan input, gunakan tombol 'Hapus Sehari' di bagian atas kolom tanggal bersangkutan.
                            Penghapusan berlaku untuk seluruh siswa pada tanggal itu. Persentase = H &divide; (H+I+S+A); hari libur tidak dihitung.
                        </p>
                    @endif
                </div>
            </div>
        </main>
    </div>

    @if($adaFilter)
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
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Sidebar Toggle
            const btnToggleSidebar = document.getElementById('btnToggleSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const sidebar = document.getElementById('sidebar');

            function toggleMenu() {
                if (sidebar) sidebar.classList.toggle('-translate-x-full');
                if (mobileOverlay) mobileOverlay.classList.toggle('hidden');
            }

            if (btnToggleSidebar) btnToggleSidebar.addEventListener('click', toggleMenu);
            if (mobileOverlay) mobileOverlay.addEventListener('click', toggleMenu);

            // 2. Pilih periode pembelajaran otomatis
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

            // 3. Konfirmasi Hapus Hari via Data Attributes
            const formHapusHari = document.getElementById('formHapusHari');
            const hapusTanggal = document.getElementById('hapusTanggal');

            document.querySelectorAll('.btn-hapus-hari').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tanggalIso = this.dataset.tanggal;
                    const tanggalTampil = this.dataset.tampil;

                    Swal.fire({
                        title: 'Hapus absen tanggal ' + tanggalTampil + '?',
                        html: 'Absen <strong>seluruh siswa</strong> pada tanggal ini akan dihapus permanen. '
                            + 'Penghapusan per siswa tidak tersedia. Setelah itu Anda dapat absen ulang dari dashboard.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#CE1B1B',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus Sehari',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold',
                            cancelButton: 'text-xs px-5 py-2.5 rounded-xl font-semibold'
                        }
                    }).then((hasil) => {
                        if (hasil.isConfirmed && formHapusHari && hapusTanggal) {
                            hapusTanggal.value = tanggalIso;
                            formHapusHari.submit();
                        }
                    });
                });
            });

            // 4. Flash Message Notifikasi
            const flash = document.getElementById('flashData');
            if (flash) {
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
            }
        });
    </script>
</body>
</html>