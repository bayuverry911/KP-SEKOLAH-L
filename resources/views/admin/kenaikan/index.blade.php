<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kenaikan Kelas - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans antialiased overflow-x-hidden">
    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="px-4 sm:px-8 pt-4 pb-2 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm"><i class="fa-solid fa-bars text-lg"></i></button>
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">KENAIKAN KELAS & KELULUSAN</h1>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-5">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">Kenaikan Kelas &amp; Kelulusan</h2>

                @if(session('success'))
                    <div class="p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl">{{ session('success') }}</div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="p-3 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl">{{ session('error') ?? $errors->first() }}</div>
                @endif

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100 space-y-5">
                    <div class="p-3 bg-sky-50 border border-sky-200 text-sky-900 text-xs rounded-xl">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Kelas siswa <b>tidak ditimpa</b>. Penempatan baru dicatat di riwayat per Tahun Pelajaran, sehingga absensi kelas lama tetap utuh.
                        Agar kelas baru berlaku, pastikan Tahun Pelajaran tujuan mencakup tanggal hari ini atau dijadikan <b>aktif</b> di Kalender Akademik.
                    </div>

                    @if($tahunList->isEmpty())
                        <p class="text-xs text-slate-500 italic">Belum ada Tahun Pelajaran. Tambahkan dulu di menu Kalender Akademik.</p>
                    @else
                        <!-- LANGKAH 1: pilih tahun & kelas asal -->
                        <form method="GET" action="{{ route('admin.kenaikan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end text-xs">
                            <div class="md:col-span-4">
                                <label class="block font-semibold text-slate-600 mb-1">Tahun Pelajaran Asal</label>
                                <select name="tahun_asal" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                    @foreach($tahunList as $t)
                                        <option value="{{ $t->id }}" {{ $tahunAsal && $tahunAsal->id === $t->id ? 'selected' : '' }}>{{ $t->nama }}{{ $t->aktif ? ' (aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block font-semibold text-slate-600 mb-1">Kelas Asal</label>
                                <select name="kelas_asal" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                    <option value="">- Pilih kelas -</option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k->id }}" {{ $kelasAsal && $kelasAsal->id === $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition"><i class="fa-solid fa-list-check mr-1"></i> Tampilkan Siswa</button>
                            </div>
                        </form>

                        @if($kelasAsal)
                            <hr class="border-slate-100">

                            <!-- LANGKAH 2: pilih siswa & tujuan -->
                            <form method="POST" action="{{ route('admin.kenaikan.proses') }}" onsubmit="return konfirmasiProses(event)">
                                @csrf
                                <input type="hidden" name="tahun_asal_id" value="{{ $tahunAsal->id }}">
                                <input type="hidden" name="kelas_asal_id" value="{{ $kelasAsal->id }}">

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs mb-4">
                                    <div class="md:col-span-4">
                                        <label class="block font-semibold text-slate-600 mb-1">Tahun Pelajaran Tujuan</label>
                                        <select name="tahun_tujuan_id" id="tahunTujuan" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                            @foreach($tahunList as $t)
                                                @if($t->id !== $tahunAsal->id)
                                                    <option value="{{ $t->id }}" {{ $tahunTujuanDefault && $tahunTujuanDefault->id === $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block font-semibold text-slate-600 mb-1">Naik ke Kelas</label>
                                        <select name="tujuan" id="tujuan" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                            @if($bolehLulus)
                                                <option value="LULUS">LULUS (kelas akhir)</option>
                                            @endif
                                            @foreach($kelasList as $k)
                                                @if($k->id !== $kelasAsal->id)
                                                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-4 flex items-end">
                                        <button type="submit" class="w-full px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow transition"><i class="fa-solid fa-arrow-up-right-dots mr-1"></i> Proses yang Dicentang</button>
                                    </div>
                                </div>

                                <div class="overflow-x-auto rounded-xl border border-slate-200">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-[#0f7a63] text-white font-bold">
                                            <tr>
                                                <th class="p-2.5 w-10 text-center"><input type="checkbox" id="pilihSemua" checked class="accent-emerald-400"></th>
                                                <th class="p-2.5 w-10">No</th>
                                                <th class="p-2.5">Nama Siswa</th>
                                                <th class="p-2.5">NISN</th>
                                                <th class="p-2.5">Kelas Sekarang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                            @forelse($siswas as $i => $s)
                                                <tr class="hover:bg-slate-50">
                                                    <td class="p-2.5 text-center"><input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" checked class="cek-siswa accent-[#19A389]"></td>
                                                    <td class="p-2.5">{{ $i + 1 }}</td>
                                                    <td class="p-2.5 font-bold text-slate-800">{{ $s->nama }}</td>
                                                    <td class="p-2.5 text-slate-500">{{ $s->nisn ?? '-' }}</td>
                                                    <td class="p-2.5">Kelas {{ $s->kelas }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="p-5 text-center text-slate-400 italic">Tidak ada siswa di kelas ini pada tahun pelajaran tersebut.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-2">Siswa yang tidak dicentang (mis. tinggal kelas) tidak diubah.</p>
                            </form>
                        @endif
                    @endif
                </div>

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Riwayat Penempatan Terakhir</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-100 text-slate-600 font-bold"><tr><th class="p-2.5">Siswa</th><th class="p-2.5">Tahun Pelajaran</th><th class="p-2.5">Kelas / Status</th><th class="p-2.5">Dicatat</th></tr></thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($riwayat as $r)
                                    <tr>
                                        <td class="p-2.5 font-semibold">{{ $r->nama }}</td>
                                        <td class="p-2.5">{{ $r->tahun }}</td>
                                        <td class="p-2.5">{{ $r->status === 'lulus' ? 'LULUS' : 'Kelas ' . $r->nama_kelas }}</td>
                                        <td class="p-2.5 text-slate-500">{{ \Carbon\Carbon::parse($r->updated_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="p-4 text-center text-slate-400 italic">Belum ada riwayat kenaikan kelas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const pilihSemua = document.getElementById('pilihSemua');
        if (pilihSemua) {
            pilihSemua.addEventListener('change', function () {
                document.querySelectorAll('.cek-siswa').forEach(function (c) { c.checked = pilihSemua.checked; });
            });
        }

        function konfirmasiProses(e) {
            const f = e.target;
            if (f.dataset.ok === '1') return true;
            const n = document.querySelectorAll('.cek-siswa:checked').length;
            if (n === 0) { uiToast('Pilih minimal satu siswa.', 'error'); return false; }
            const t = document.getElementById('tujuan');
            const label = t.options[t.selectedIndex].text;
            uiConfirm({
                judul: 'Proses Kenaikan Kelas',
                pesan: 'Proses ' + n + ' siswa menjadi: ' + label + '? Riwayat kelas lama tetap tersimpan.',
                ok: 'Ya, Proses'
            }, function () { f.dataset.ok = '1'; f.submit(); });
            return false;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
