<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin - SMP Djojoredjo</title>
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
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PENGAJUAN IZIN</h1>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-5">
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight uppercase">Pengajuan Izin Guru</h2>

                @if(session('success'))
                    <div class="p-3 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl">{{ session('success') }}</div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="p-3 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl">{{ session('error') ?? $errors->first() }}</div>
                @endif

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Ajukan Izin Baru</h3>
                    <p class="text-[11px] text-slate-500 mb-4">Setelah disetujui admin, presensi Anda pada tanggal tersebut otomatis terisi (Sakit/Izin) dan terkunci.</p>

                    <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4 text-xs">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Jenis Izin</label>
                            <select name="jenis" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                                <option value="Sakit" {{ old('jenis') === 'Sakit' ? 'selected' : '' }}>Sakit (surat dokter)</option>
                                <option value="Dinas" {{ old('jenis') === 'Dinas' ? 'selected' : '' }}>Dinas (surat tugas)</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block font-semibold text-slate-600 mb-1">Bukti (JPG/PNG/PDF, maks 2 MB)</label>
                            <input type="file" name="bukti" required accept=".jpg,.jpeg,.png,.pdf" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px]">
                        </div>
                        <div class="md:col-span-12">
                            <label class="block font-semibold text-slate-600 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="2" required maxlength="500" placeholder="Contoh: Demam tinggi, rawat jalan / Workshop kurikulum di Dinas Pendidikan" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl">{{ old('keterangan') }}</textarea>
                        </div>
                        <div class="md:col-span-12">
                            <button type="submit" class="px-6 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow transition">
                                <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-xl border border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Riwayat Pengajuan Saya</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#0f7a63] text-white font-bold">
                                <tr><th class="p-2.5">Jenis</th><th class="p-2.5">Tanggal</th><th class="p-2.5">Keterangan</th><th class="p-2.5">Bukti</th><th class="p-2.5">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($izins as $iz)
                                    <tr>
                                        <td class="p-2.5 font-bold">{{ $iz->jenis }}</td>
                                        <td class="p-2.5 whitespace-nowrap">{{ $iz->tanggal_mulai->format('d/m/Y') }}{{ $iz->tanggal_selesai->ne($iz->tanggal_mulai) ? ' - ' . $iz->tanggal_selesai->format('d/m/Y') : '' }}</td>
                                        <td class="p-2.5">{{ $iz->keterangan }}</td>
                                        <td class="p-2.5">
                                            <button type="button" class="btn-bukti text-[#138A73] font-semibold hover:underline"
                                                    data-url="{{ route('guru.izin.bukti', $iz->id) }}" data-nama="{{ $iz->bukti_nama }}">
                                                <i class="fa-solid fa-paperclip"></i> Lihat
                                            </button>
                                        </td>
                                        <td class="p-2.5">
                                            @if($iz->status === 'disetujui')
                                                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">Disetujui</span>
                                            @elseif($iz->status === 'ditolak')
                                                <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">Ditolak</span>
                                                @if($iz->catatan_admin)<div class="text-[10px] text-slate-500 mt-1">{{ $iz->catatan_admin }}</div>@endif
                                            @else
                                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold text-[10px]">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-4 text-center text-slate-400 italic">Belum ada pengajuan izin.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL BUKTI (tanpa pindah tab) -->
    <div id="modalBukti" class="hidden fixed inset-0 z-[60] bg-slate-900/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                <h3 id="buktiNama" class="text-sm font-bold text-slate-900 truncate"></h3>
                <button type="button" id="buktiTutup" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-semibold"><i class="fa-solid fa-xmark mr-1"></i> Tutup</button>
            </div>
            <iframe id="buktiFrame" title="Bukti" class="flex-1 w-full border-0 bg-slate-100"></iframe>
        </div>
    </div>

    <script>
        const modalBukti = document.getElementById('modalBukti');
        const buktiFrame = document.getElementById('buktiFrame');

        document.querySelectorAll('.btn-bukti').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('buktiNama').textContent = btn.dataset.nama || 'Bukti';
                buktiFrame.src = btn.dataset.url;
                modalBukti.classList.remove('hidden');
            });
        });

        function tutupBukti() { modalBukti.classList.add('hidden'); buktiFrame.src = 'about:blank'; }
        document.getElementById('buktiTutup').addEventListener('click', tutupBukti);
        modalBukti.addEventListener('click', function (e) { if (e.target === modalBukti) tutupBukti(); });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    </script>
</body>
</html>
