<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        PRESENSI GURU • PENGAJUAN IZIN
                    </h1>
                </div>

                <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60 shrink-0">
                    <i class="fa-regular fa-calendar text-[#19A389]"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-6xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Administrasi Kehadiran Guru</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Pengajuan Izin Guru</h2>
                            <p class="text-xs text-white/80 mt-1">Ajukan permohonan izin sakit atau dinas luar beserta dokumen pendukung untuk verifikasi admin</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>{{ $izins->count() }} Riwayat Pengajuan</span>
                        </div>
                    </div>

                    <!-- FLASH ALERTS -->
                    @if(session('success'))
                        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5 shadow-sm">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error') || $errors->any())
                        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') ?? $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- CARD FORM PENGAJUAN IZIN -->
                    <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-xl border border-slate-100">
                        <div class="flex items-start gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0 mt-0.5">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Ajukan Izin Baru</h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Setelah disetujui admin, presensi Anda pada tanggal tersebut otomatis terisi (Sakit/Izin) dan terkunci.</p>
                            </div>
                        </div>

                        <form action="{{ route('guru.izin.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            @csrf
                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Jenis Izin</label>
                                <select name="jenis"
                                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-bold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="Sakit" {{ old('jenis') === 'Sakit' ? 'selected' : '' }}>Sakit (surat dokter)</option>
                                    <option value="Dinas" {{ old('jenis') === 'Dinas' ? 'selected' : '' }}>Dinas (surat tugas)</option>
                                </select>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tanggal Izin</label>
                                <input type="date" name="tanggal" value="{{ old('tanggal', old('tanggal_mulai')) }}" required
                                       class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Bukti (JPG/PNG/PDF, maks 2 MB)</label>
                                <input type="file" name="bukti" required accept=".jpg,.jpeg,.png,.pdf"
                                       class="w-full p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 file:mr-2.5 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-teal-50 file:text-[#138A73] hover:file:bg-teal-100 focus:outline-none focus:border-[#19A389] transition">
                            </div>

                            <div class="md:col-span-12">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Keterangan</label>
                                <textarea name="keterangan" rows="2" required maxlength="500" placeholder="Contoh: Demam tinggi, rawat jalan / Workshop kurikulum di Dinas Pendidikan"
                                          class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">{{ old('keterangan') }}</textarea>
                            </div>

                            <div class="md:col-span-12 pt-1 flex justify-end">
                                <button type="submit"
                                        class="w-full sm:w-auto px-6 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Kirim Pengajuan</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- CARD RIWAYAT PENGAJUAN SAYA -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-[#19A389] flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Riwayat Pengajuan Saya</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400">Daftar permohonan izin yang telah diajukan beserta status verifikasi admin</p>
                            </div>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($izins as $iz)
                                <div class="p-4 space-y-2.5 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <span class="font-extrabold text-sm text-slate-900">{{ $iz->jenis }}</span>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                <i class="fa-regular fa-calendar mr-1 text-[#19A389]"></i>{{ \Carbon\Carbon::parse($iz->tanggal ?? $iz->tanggal_mulai)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        @if($iz->status === 'disetujui')
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-extrabold text-[10px]">Disetujui</span>
                                        @elseif($iz->status === 'ditolak')
                                            <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-[10px]">Ditolak</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200 font-extrabold text-[10px]">Menunggu</span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ $iz->keterangan }}</p>

                                    @if($iz->status === 'ditolak' && $iz->catatan_admin)
                                        <div class="p-2.5 bg-rose-50 border border-rose-200/70 rounded-xl text-[11px] text-rose-800">
                                            <span class="font-bold">Catatan Admin:</span> {{ $iz->catatan_admin }}
                                        </div>
                                    @endif

                                    <div class="pt-1 flex justify-end">
                                        <button type="button"
                                                class="btn-bukti px-3 py-1.5 bg-teal-50 text-[#138A73] border border-teal-100 rounded-lg text-xs font-bold hover:bg-teal-100 transition inline-flex items-center gap-1.5"
                                                data-url="{{ route('guru.izin.bukti', $iz->id) }}"
                                                data-nama="{{ $iz->bukti_nama }}">
                                            <i class="fa-solid fa-paperclip"></i>
                                            <span>Lihat Bukti</span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada pengajuan izin.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#19A389] text-white font-bold uppercase tracking-wider text-[11px]">
                                        <th class="p-3.5">Jenis</th>
                                        <th class="p-3.5 whitespace-nowrap">Tanggal</th>
                                        <th class="p-3.5">Keterangan</th>
                                        <th class="p-3.5 text-center">Bukti</th>
                                        <th class="p-3.5">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($izins as $iz)
                                        <tr class="hover:bg-teal-50/40 transition">
                                            <td class="p-3.5 font-extrabold text-slate-900 whitespace-nowrap">{{ $iz->jenis }}</td>
                                            <td class="p-3.5 whitespace-nowrap font-medium text-slate-600">{{ \Carbon\Carbon::parse($iz->tanggal ?? $iz->tanggal_mulai)->format('d/m/Y') }}</td>
                                            <td class="p-3.5 text-slate-700">{{ $iz->keterangan }}</td>
                                            <td class="p-3.5 text-center whitespace-nowrap">
                                                <button type="button"
                                                        class="btn-bukti px-3 py-1.5 bg-teal-50 text-[#138A73] border border-teal-100 rounded-lg font-bold hover:bg-teal-100 transition inline-flex items-center gap-1"
                                                        data-url="{{ route('guru.izin.bukti', $iz->id) }}"
                                                        data-nama="{{ $iz->bukti_nama }}">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                    <span>Lihat</span>
                                                </button>
                                            </td>
                                            <td class="p-3.5">
                                                @if($iz->status === 'disetujui')
                                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-extrabold text-[10px]">Disetujui</span>
                                                @elseif($iz->status === 'ditolak')
                                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-[10px]">Ditolak</span>
                                                    @if($iz->catatan_admin)
                                                        <div class="text-[11px] text-slate-500 mt-1">{{ $iz->catatan_admin }}</div>
                                                    @endif
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200 font-extrabold text-[10px]">Menunggu</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-slate-400 italic">Belum ada pengajuan izin.</td>
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

    <!-- MODAL BUKTI (tanpa pindah tab) -->
    <div id="modalBukti" class="hidden fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center p-3 sm:p-6">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden border border-slate-100">
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#19A389] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h3 id="buktiNama" class="text-sm font-extrabold text-slate-900 truncate"></h3>
                </div>
                <button type="button" id="buktiTutup" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition shrink-0 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Tutup</span>
                </button>
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

        function tutupBukti() {
            modalBukti.classList.add('hidden');
            buktiFrame.src = 'about:blank';
        }
        document.getElementById('buktiTutup').addEventListener('click', tutupBukti);
        modalBukti.addEventListener('click', function (e) { if (e.target === modalBukti) tutupBukti(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modalBukti.classList.contains('hidden')) tutupBukti();
        });
    </script>
</body>
</html>