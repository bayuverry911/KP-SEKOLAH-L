<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenaikan Kelas & Kelulusan - SMP Djojoredjo</title>
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
                        MASTER DATA • KENAIKAN KELAS & KELULUSAN
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($tahunAsal)
                        <span class="hidden sm:inline-flex items-center px-3 py-1.5 bg-white rounded-xl shadow-sm border border-slate-200/60 text-xs font-semibold text-slate-600">
                            <i class="fa-solid fa-graduation-cap text-[#19A389] mr-1.5"></i> TA Asal: <b class="ml-1">{{ $tahunAsal->nama }}</b>
                        </span>
                    @endif
                    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : (Route::has('dashboard') ? route('dashboard') : '#') }}"
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
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Manajemen Akademik Siswa</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Kenaikan Kelas & Kelulusan</h2>
                            <p class="text-xs text-white/80 mt-1">Proses perpindahan tingkat kelas atau kelulusan siswa per tahun pelajaran tanpa menghapus riwayat absensi lama</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-arrow-up-right-dots"></i>
                            <span>{{ $kelasAsal ? 'Kelas ' . $kelasAsal->nama_kelas : 'Pilih Kelas Asal' }}</span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- CARD UTAMA PROSES KENAIKAN KELAS -->
                    <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">
                        <!-- Info Banner -->
                        <div class="p-3.5 sm:p-4 bg-sky-50 border border-sky-200 text-sky-900 text-xs rounded-2xl flex items-start gap-2.5 leading-relaxed">
                            <i class="fa-solid fa-circle-info text-sky-600 text-sm mt-0.5 shrink-0"></i>
                            <div>
                                Kelas siswa <b>tidak ditimpa</b>. Penempatan baru dicatat di riwayat per Tahun Pelajaran sehingga data absensi kelas lama tetap utuh.
                                Agar kelas baru langsung berlaku, pastikan Tahun Pelajaran tujuan mencakup tanggal hari ini atau dijadikan <b>aktif</b> di menu <b>Kalender Akademik</b>.
                            </div>
                        </div>

                        @if($tahunList->isEmpty())
                            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-xs text-slate-500">
                                <i class="fa-regular fa-calendar-xmark text-2xl block mb-2 text-slate-300"></i>
                                Belum ada Tahun Pelajaran. Silakan tambahkan terlebih dahulu di menu <b>Kalender Akademik</b>.
                            </div>
                        @else
                            <!-- LANGKAH 1: PILIH TAHUN & KELAS ASAL -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#0D6353] text-[10px] font-extrabold uppercase tracking-wider">Langkah 1</span>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-800">Pilih Tahun Pelajaran & Kelas Asal</h3>
                                </div>

                                <form method="GET" action="{{ route('admin.kenaikan.index') }}"
                                      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-end">
                                    <div class="lg:col-span-4">
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Tahun Pelajaran Asal</label>
                                        <select name="tahun_asal"
                                                class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
                                            @foreach($tahunList as $t)
                                                <option value="{{ $t->id }}" {{ $tahunAsal && $tahunAsal->id === $t->id ? 'selected' : '' }}>
                                                    {{ $t->nama }}{{ $t->aktif ? ' (Aktif)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="lg:col-span-4">
                                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Kelas Asal</label>
                                        <select name="kelas_asal"
                                                class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
                                            <option value="">-- Pilih Kelas --</option>
                                            @foreach($kelasList as $k)
                                                <option value="{{ $k->id }}" {{ $kelasAsal && $kelasAsal->id === $k->id ? 'selected' : '' }}>
                                                    Kelas {{ $k->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2 lg:col-span-4">
                                        <button type="submit"
                                                class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-extrabold rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2">
                                            <i class="fa-solid fa-list-check"></i>
                                            <span>Tampilkan Daftar Siswa</span>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if($kelasAsal)
                                <hr class="border-slate-100">

                                <!-- LANGKAH 2: PILIH SISWA & TUJUAN -->
                                <form id="formKenaikan" method="POST" action="{{ route('admin.kenaikan.proses') }}" onsubmit="return konfirmasiProses(event)" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="tahun_asal_id" value="{{ $tahunAsal->id }}">
                                    <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}">

                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#0D6353] text-[10px] font-extrabold uppercase tracking-wider">Langkah 2</span>
                                            <h3 class="text-xs sm:text-sm font-bold text-slate-800">Tentukan Tujuan & Centang Siswa yang Naik / Lulus</h3>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-500">
                                            Terpilih: <b id="jumlahTerpilih" class="text-[#19A389]">{{ $siswas->count() }}</b> dari <b>{{ $siswas->count() }}</b> siswa
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl items-end">
                                        <div class="lg:col-span-4">
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                                Tahun Pelajaran Tujuan <span class="text-rose-500">*</span>
                                            </label>
                                            <select name="tahun_tujuan_id" id="tahunTujuan" required
                                                    class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
                                                @foreach($tahunList as $t)
                                                    @if($t->id !== $tahunAsal->id)
                                                        <option value="{{ $t->id }}" {{ $tahunTujuanDefault && $tahunTujuanDefault->id === $t->id ? 'selected' : '' }}>
                                                            {{ $t->nama }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="lg:col-span-4">
                                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                                Naik ke Kelas / Status <span class="text-rose-500">*</span>
                                            </label>
                                            <select name="tujuan" id="tujuan" required
                                                    class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-700 focus:outline-none focus:border-[#19A389] transition">
                                                @if($bolehLulus)
                                                    <option value="LULUS">LULUS (Kelas Akhir)</option>
                                                @endif
                                                @foreach($kelasList as $k)
                                                    @if($k->id !== $kelasAsal->id)
                                                        <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2 lg:col-span-4">
                                            <button type="submit"
                                                    class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white text-xs font-extrabold rounded-xl shadow-md transition inline-flex items-center justify-center gap-2">
                                                <i class="fa-solid fa-arrow-up-right-dots"></i>
                                                <span>Proses yang Dicentang</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bar Kontrol Pilih Semua & Cari Siswa -->
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                                        <label class="inline-flex items-center gap-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/80 w-fit">
                                            <input type="checkbox" id="pilihSemua" checked class="w-4 h-4 rounded accent-[#19A389] cursor-pointer">
                                            <span>Pilih / Batalkan Semua Siswa</span>
                                        </label>

                                        <div class="relative w-full sm:w-64">
                                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                            <input type="text" id="cariSiswaKenaikan" onkeyup="filterSiswaKenaikan()"
                                                   placeholder="Cari nama atau NISN..."
                                                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        </div>
                                    </div>

                                    <!-- Daftar Siswa Responsif (Single DOM Checkbox per Siswa) -->
                                    <div class="rounded-2xl border border-slate-200 overflow-hidden">
                                        <!-- Header Kolom Desktop -->
                                        <div class="hidden md:grid md:grid-cols-12 bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px] px-4 py-3.5 items-center">
                                            <div class="col-span-1 text-center">Pilih</div>
                                            <div class="col-span-1 text-center">No</div>
                                            <div class="col-span-5">Nama Siswa</div>
                                            <div class="col-span-3">NISN</div>
                                            <div class="col-span-2 text-right">Kelas Asal</div>
                                        </div>

                                        <div id="daftarSiswaKenaikan" class="divide-y divide-slate-100 bg-white">
                                            @forelse($siswas as $i => $s)
                                                <label class="siswa-row grid grid-cols-12 items-center px-3.5 sm:px-4 py-3 hover:bg-emerald-50/40 transition cursor-pointer select-none gap-2">
                                                    <div class="col-span-2 md:col-span-1 flex justify-center">
                                                        <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" checked
                                                               class="cek-siswa w-4 h-4 rounded accent-[#19A389] cursor-pointer">
                                                    </div>
                                                    <div class="hidden md:block md:col-span-1 text-center font-semibold text-xs text-slate-400">
                                                        {{ $i + 1 }}
                                                    </div>
                                                    <div class="col-span-7 md:col-span-5 min-w-0">
                                                        <p class="font-bold text-xs sm:text-sm md:text-xs text-slate-800 truncate">{{ $s->nama }}</p>
                                                        <p class="md:hidden text-[11px] text-slate-500 mt-0.5 font-mono">NISN: {{ $s->nisn ?? '-' }}</p>
                                                    </div>
                                                    <div class="hidden md:block md:col-span-3 font-mono text-xs text-slate-500">
                                                        {{ $s->nisn ?? '-' }}
                                                    </div>
                                                    <div class="col-span-3 md:col-span-2 text-right">
                                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[11px] whitespace-nowrap">
                                                            Kelas {{ $s->kelas }}
                                                        </span>
                                                    </div>
                                                </label>
                                            @empty
                                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                                    <i class="fa-regular fa-user text-2xl block mb-2 text-slate-300"></i>
                                                    Tidak ada siswa di kelas ini pada tahun pelajaran tersebut.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-info text-slate-400"></i>
                                        <span>Siswa yang <b>tidak dicentang</b> (misalnya tinggal kelas) tidak akan dipindahkan.</span>
                                    </p>
                                </form>
                            @endif
                        @endif
                    </div>

                    <!-- CARD RIWAYAT PENEMPATAN TERAKHIR -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Riwayat Penempatan Terakhir</h3>
                                <p class="text-[11px] text-slate-400">Catatan pemrosesan kenaikan kelas dan kelulusan siswa terbaru</p>
                            </div>
                        </div>

                        <!-- TAMPILAN HP RIWAYAT (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($riwayat as $r)
                                <div class="p-4 space-y-2 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="font-bold text-sm text-slate-800">{{ $r->nama }}</p>
                                        @if($r->status === 'lulus')
                                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-extrabold text-[10px] shrink-0">LULUS</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-[#0D6353] font-extrabold text-[10px] shrink-0">Kelas {{ $r->nama_kelas }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                                        <span>TA: <b class="text-slate-700">{{ $r->tahun }}</b></span>
                                        <span>{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    Belum ada riwayat kenaikan kelas.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP RIWAYAT (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4 w-12 text-center">No</th>
                                        <th class="p-4">Nama Siswa</th>
                                        <th class="p-4">Tahun Pelajaran</th>
                                        <th class="p-4">Kelas / Status</th>
                                        <th class="p-4 whitespace-nowrap">Dicatat Pada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($riwayat as $r)
                                        <tr class="hover:bg-slate-50/90 transition">
                                            <td class="p-4 text-center font-semibold text-slate-400">{{ $loop->iteration }}</td>
                                            <td class="p-4 font-bold text-slate-800">{{ $r->nama }}</td>
                                            <td class="p-4 font-semibold text-slate-700">{{ $r->tahun }}</td>
                                            <td class="p-4">
                                                @if($r->status === 'lulus')
                                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 font-extrabold text-[11px]">LULUS</span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-[#0D6353] border border-emerald-200 font-bold text-[11px]">Kelas {{ $r->nama_kelas }}</span>
                                                @endif
                                            </td>
                                            <td class="p-4 text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                                Belum ada riwayat kenaikan kelas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        const pilihSemua = document.getElementById('pilihSemua');
        const jumlahTerpilihEl = document.getElementById('jumlahTerpilih');

        function updateCounterTerpilih() {
            if (!jumlahTerpilihEl) return;
            const totalChecked = document.querySelectorAll('.cek-siswa:checked').length;
            jumlahTerpilihEl.textContent = totalChecked;

            const allChecks = document.querySelectorAll('.cek-siswa');
            if (pilihSemua && allChecks.length > 0) {
                pilihSemua.checked = (totalChecked === allChecks.length);
                pilihSemua.indeterminate = (totalChecked > 0 && totalChecked < allChecks.length);
            }
        }

        if (pilihSemua) {
            pilihSemua.addEventListener('change', function () {
                document.querySelectorAll('.siswa-row').forEach(function (row) {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.cek-siswa');
                        if (cb) cb.checked = pilihSemua.checked;
                    }
                });
                updateCounterTerpilih();
            });
        }

        document.querySelectorAll('.cek-siswa').forEach(function (cb) {
            cb.addEventListener('change', updateCounterTerpilih);
        });

        function filterSiswaKenaikan() {
            const keyword = document.getElementById('cariSiswaKenaikan').value.toLowerCase();
            document.querySelectorAll('.siswa-row').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(keyword) ? '' : 'none';
            });
        }

        function konfirmasiProses(e) {
            const f = e.target;
            if (f.dataset.ok === '1') return true;

            e.preventDefault();
            const n = document.querySelectorAll('.cek-siswa:checked').length;
            if (n === 0) {
                Swal.fire({
                    title: 'Belum Ada Siswa Dipilih',
                    text: 'Silakan centang minimal satu siswa terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonColor: '#19A389',
                    confirmButtonText: 'Mengerti'
                });
                return false;
            }

            const t = document.getElementById('tujuan');
            const tahunTujuanEl = document.getElementById('tahunTujuan');
            const labelTujuan = t.options[t.selectedIndex].text;
            const labelTahun = tahunTujuanEl ? tahunTujuanEl.options[tahunTujuanEl.selectedIndex].text : '';

            Swal.fire({
                title: 'Proses Kenaikan Kelas?',
                text: `Proses ${n} siswa ke "${labelTujuan}" pada Tahun Pelajaran ${labelTahun}? Riwayat kelas lama tetap tersimpan.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#19A389',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Proses Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    f.dataset.ok = '1';
                    f.submit();
                }
            });

            return false;
        }
    </script>
</body>
</html>