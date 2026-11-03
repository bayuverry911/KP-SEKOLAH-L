@use('App\Services\AbsenSiswaService', 'Absen')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Akademik - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">KALENDER AKADEMIK</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-6 mt-2">

                @if(session('success'))
                    <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i><span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i><span>{{ session('error') ?? $errors->first() }}</span>
                    </div>
                @endif

                <!-- ================= TAHUN PELAJARAN ================= -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-[#19A389]"></i> Tahun Pelajaran
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tahun pelajaran <b>aktif</b> menjadi default filter tanggal di akun guru (Rekap Absen Siswa). Hari libur yang Anda tandai otomatis tidak dihitung sebagai hari efektif.</p>
                    </div>

                    <form action="{{ route('admin.kalender.tahun.store') }}" method="POST"
                          class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Tahun Pelajaran</label>
                            <input type="text" name="nama" required maxlength="30" placeholder="2026/2027" value="{{ old('nama') }}"
                                   class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Mulai</label>
                            <input type="date" name="tanggal_mulai" required value="{{ old('tanggal_mulai') }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Selesai</label>
                            <input type="date" name="tanggal_selesai" required value="{{ old('tanggal_selesai') }}" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl transition">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Tahun Pelajaran
                            </button>
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-2">
                        @forelse($tahunList as $t)
                            <div class="flex items-center gap-1 rounded-xl border {{ $tahun && $tahun->id === $t->id ? 'border-[#19A389] bg-teal-50' : 'border-slate-200 bg-white' }} pl-3 pr-1 py-1 text-xs">
                                <a href="{{ route('admin.kalender.index', ['tahun' => $t->id]) }}" class="font-bold text-slate-800">
                                    {{ $t->nama }}
                                    <span class="font-normal text-slate-500">({{ $t->tanggal_mulai->format('d/m/Y') }} - {{ $t->tanggal_selesai->format('d/m/Y') }})</span>
                                </a>
                                @if($t->aktif)
                                    <span class="ml-1 px-2 py-0.5 rounded-full bg-[#19A389] text-white text-[10px] font-bold">AKTIF</span>
                                @else
                                    <form action="{{ route('admin.kalender.tahun.aktif', $t->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="ml-1 px-2 py-0.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold">Jadikan Aktif</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.kalender.tahun.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tahun pelajaran ini? Data hari libur tetap tersimpan.')">
                                    @csrf @method('DELETE')
                                    <button class="px-2 py-1 text-rose-600 hover:text-rose-800" title="Hapus"><i class="fa-solid fa-trash text-[11px]"></i></button>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada Tahun Pelajaran. Tambahkan dulu (mis. 2026/2027: 13/07/2026 - 30/06/2027).</p>
                        @endforelse
                    </div>
                </div>

                <!-- ================= KALENDER PER BULAN ================= -->
                @if($tahun)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <i class="fa-solid fa-calendar-xmark text-rose-500"></i> Hari Libur Tahun Pelajaran {{ $tahun->nama }}
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">Klik tanggal untuk menandai / mengubah / menghapus hari libur. Total <b>{{ $liburs->count() }}</b> hari libur.</p>
                            </div>

                            <form action="{{ route('admin.kalender.libur.tetap') }}" method="POST" class="text-xs">
                                @csrf
                                <input type="hidden" name="tahun_id" value="{{ $tahun->id }}">
                                <button type="submit" class="px-3 py-2 bg-teal-50 text-[#138A73] font-semibold rounded-xl hover:bg-teal-100 transition">
                                    <i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Isi libur tanggal tetap
                                </button>
                            </form>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($blok as $b)
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <div class="bg-[#0f7a63] text-white text-xs font-bold px-3 py-2 flex justify-between">
                                        <span>{{ $b['judul'] }}</span>
                                        @if(count($b['libur']))<span class="font-semibold text-rose-200">{{ count($b['libur']) }} libur</span>@endif
                                    </div>

                                    <table class="w-full text-center text-[11px] border-separate border-spacing-0">
                                        <thead>
                                            <tr class="text-slate-500">
                                                @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)
                                                    <th class="py-1 font-semibold border-b border-slate-100 {{ in_array($h, ['Sab','Min']) ? 'text-rose-400' : '' }}">{{ $h }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($b['minggu'] as $minggu)
                                                <tr>
                                                    @foreach($minggu as $c)
                                                        @if($c === null)
                                                            <td class="p-0"></td>
                                                        @else
                                                            <td class="p-0.5">
                                                                @php
                                                                    $kelasSel = $c['luar'] ? 'text-slate-300 cursor-not-allowed'
                                                                        : ($c['libur'] ? 'bg-rose-500 text-white font-bold hover:bg-rose-600'
                                                                        : ($c['weekend'] ? 'bg-slate-50 text-rose-400 hover:bg-slate-100' : 'text-slate-700 hover:bg-teal-50'));
                                                                @endphp
                                                                <button type="button"
                                                                        class="btn-tgl w-full h-7 rounded-md {{ $kelasSel }}"
                                                                        {{ $c['luar'] ? 'disabled' : '' }}
                                                                        data-tgl="{{ $c['tgl'] }}"
                                                                        data-tampil="{{ \Carbon\Carbon::parse($c['tgl'])->format('d/m/Y') }}"
                                                                        data-hari="{{ Absen::namaHari(\Carbon\Carbon::parse($c['tgl'])) }}"
                                                                        data-libur="{{ $c['libur'] }}"
                                                                        data-jenis="{{ $c['jenis'] }}"
                                                                        data-id="{{ $c['id'] }}"
                                                                        title="{{ $c['libur'] }}">{{ $c['hari'] }}</button>
                                                            </td>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    @if(count($b['libur']))
                                        <ul class="px-3 py-2 text-[10px] text-slate-600 border-t border-slate-100 space-y-0.5">
                                            @foreach($b['libur'] as $l)
                                                <li><b class="text-rose-600">{{ $l['hari'] }}</b> &ndash; {{ $l['keterangan'] }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ================= PERIODE PEMBELAJARAN ================= -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-[#19A389]"></i> Periode Pembelajaran
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Muncul sebagai pilihan "Rentang Pembelajaran" di Rekap Absen Siswa. Periode yang mencakup hari ini otomatis menjadi tampilan awal guru.</p>
                    </div>

                    <form action="{{ route('admin.kalender.periode.store') }}" method="POST"
                          class="grid grid-cols-1 md:grid-cols-12 gap-3 text-xs p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                        @csrf
                        <div class="md:col-span-4">
                            <label class="block font-semibold text-slate-600 mb-1">Nama Periode</label>
                            <input type="text" name="nama" required maxlength="100" placeholder="Contoh: Pertengahan Oktober s/d UAS" class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Selesai</label>
                            <input type="date" name="tanggal_selesai" required class="w-full p-2.5 bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl transition"><i class="fa-solid fa-plus mr-1"></i> Tambah</button>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gradient-to-r from-[#19A389] to-[#138A73] text-white uppercase font-bold">
                                <tr><th class="p-3">Nama Periode</th><th class="p-3">Mulai</th><th class="p-3">Selesai</th><th class="p-3 text-center">Aksi</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @forelse($periodes as $p)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-semibold text-slate-800">{{ $p->nama }}</td>
                                        <td class="p-3">{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                                        <td class="p-3">{{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                                        <td class="p-3 text-center">
                                            <form action="{{ route('admin.kalender.periode.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus periode ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-rose-600 hover:text-rose-800 font-semibold"><i class="fa-solid fa-trash text-xs"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-slate-400 italic">Belum ada periode pembelajaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL TANDAI HARI LIBUR -->
    <div id="modalLibur" data-url-hapus="{{ route('admin.kalender.libur.destroy', ['libur' => 0]) }}" class="hidden fixed inset-0 z-[60] bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 shadow-2xl w-full max-w-sm">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 id="liburJudul" class="text-base font-bold text-slate-900">Tandai Hari Libur</h3>
                    <p id="liburTanggal" class="text-xs text-slate-500"></p>
                </div>
                <button type="button" id="btnTutupLibur" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('admin.kalender.libur.store') }}" method="POST" id="formLiburBaru" class="space-y-3 text-xs">
                @csrf
                <input type="hidden" name="tanggal" id="liburInputTanggal">
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Keterangan</label>
                    <input type="text" name="keterangan" id="liburKeterangan" required maxlength="100" placeholder="Contoh: Hari Raya Idul Fitri" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Jenis</label>
                    <select name="jenis" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="nasional">Libur Nasional / Cuti Bersama</option>
                        <option value="sekolah">Libur Sekolah</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl transition">Tandai Libur</button>
            </form>

            <form method="POST" id="formLiburHapus" class="hidden space-y-3 text-xs" onsubmit="return confirm('Hapus hari libur ini?')">
                @csrf @method('DELETE')
                <p id="liburAda" class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 font-semibold"></p>
                <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition">
                    <i class="fa-solid fa-trash mr-1"></i> Hapus Hari Libur
                </button>
            </form>
        </div>
    </div>

    <script>
        const modalLibur = document.getElementById('modalLibur');
        const URL_HAPUS_LIBUR = modalLibur.dataset.urlHapus;
        const formBaru = document.getElementById('formLiburBaru');
        const formHapus = document.getElementById('formLiburHapus');

        document.querySelectorAll('.btn-tgl').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('liburTanggal').textContent = btn.dataset.hari + ', ' + btn.dataset.tampil;

                if (btn.dataset.id) {
                    document.getElementById('liburJudul').textContent = 'Hari Libur';
                    document.getElementById('liburAda').textContent = btn.dataset.libur + ' (' + btn.dataset.jenis + ')';
                    formHapus.action = URL_HAPUS_LIBUR.replace(/\/0$/, '/' + btn.dataset.id);
                    formBaru.classList.add('hidden');
                    formHapus.classList.remove('hidden');
                } else {
                    document.getElementById('liburJudul').textContent = 'Tandai Hari Libur';
                    document.getElementById('liburInputTanggal').value = btn.dataset.tgl;
                    document.getElementById('liburKeterangan').value = '';
                    formHapus.classList.add('hidden');
                    formBaru.classList.remove('hidden');
                }

                modalLibur.classList.remove('hidden');
            });
        });

        document.getElementById('btnTutupLibur').addEventListener('click', function () { modalLibur.classList.add('hidden'); });
        modalLibur.addEventListener('click', function (e) { if (e.target === modalLibur) modalLibur.classList.add('hidden'); });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
