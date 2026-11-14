<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Izin Guru - SMP Djojoredjo</title>
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
                        PRESENSI • PERSETUJUAN IZIN GURU
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
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
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Administrasi Kehadiran Pendidik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Persetujuan Izin Guru</h2>
                            <p class="text-xs text-white/80 mt-1">Verifikasi pengajuan izin atau sakit guru beserta lampiran bukti pendukung</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <div class="bg-amber-400/20 backdrop-blur-sm border border-amber-200/30 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 text-amber-100">
                                <i class="fa-solid fa-hourglass-half"></i>
                                <span>{{ $hitung['menunggu'] ?? 0 }} Menunggu</span>
                            </div>
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

                    <!-- CARD DAFTAR PENGAJUAN IZIN -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Filter Status Tabs -->
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex flex-wrap gap-2 text-xs">
                                @foreach(['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'semua' => 'Semua'] as $k => $label)
                                    <a href="{{ route('admin.izin.index', ['status' => $k]) }}"
                                       class="px-4 py-2 rounded-xl font-bold transition inline-flex items-center gap-1.5 {{ $status === $k ? 'bg-[#19A389] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                        <span>{{ $label }}</span>
                                        @if($k !== 'semua')
                                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === $k ? 'bg-white/20 text-white' : 'bg-white text-slate-700' }}">
                                                {{ $hitung[$k] ?? 0 }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400">
                                Menampilkan {{ $izins->count() }} dari {{ $izins->total() }} pengajuan
                            </span>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($izins as $iz)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-bold text-sm text-slate-900 truncate">{{ $iz->user->name ?? '-' }}</p>
                                            <div class="flex flex-wrap items-center gap-2 mt-1 text-[11px] text-slate-500">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold uppercase">{{ $iz->jenis }}</span>
                                                <span><i class="fa-regular fa-calendar mr-1 text-[#19A389]"></i>{{ \Carbon\Carbon::parse($iz->tanggal ?? $iz->tanggal_mulai)->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="shrink-0">
                                            @if($iz->status === 'disetujui')
                                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">Disetujui</span>
                                            @elseif($iz->status === 'ditolak')
                                                <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[10px]">Ditolak</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px]">Menunggu</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-700 leading-relaxed">
                                        {{ $iz->keterangan }}
                                        @if($iz->status === 'ditolak' && $iz->catatan_admin)
                                            <div class="mt-2 pt-2 border-t border-slate-200/80 text-[11px] text-rose-600 font-semibold">
                                                <i class="fa-solid fa-comment-dots mr-1"></i> Catatan: {{ $iz->catatan_admin }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                        <button type="button"
                                                class="btn-bukti px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-[#0D6353] border border-emerald-200 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 transition"
                                                data-url="{{ route('admin.izin.bukti', $iz->id) }}"
                                                data-nama="{{ $iz->bukti_nama }}">
                                            <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                                        </button>

                                        @if($iz->status === 'menunggu')
                                            <div class="flex items-center gap-2 ml-auto">
                                                <form id="form-setujui-mobile-{{ $iz->id }}" action="{{ route('admin.izin.setujui', $iz->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="button"
                                                            data-form="form-setujui-mobile-{{ $iz->id }}"
                                                            data-guru="{{ $iz->user->name ?? 'Guru' }}"
                                                            onclick="confirmSetujuiIzin(this)"
                                                            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                                                        <i class="fa-solid fa-check mr-1"></i> Setujui
                                                    </button>
                                                </form>
                                                <button type="button"
                                                        class="btn-tolak px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                                                        data-url="{{ route('admin.izin.tolak', $iz->id) }}"
                                                        data-guru="{{ $iz->user->name ?? '-' }}">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                    Tidak ada pengajuan pada kategori ini.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#19A389] text-white uppercase font-bold tracking-wider text-[11px]">
                                    <tr>
                                        <th class="p-4">Guru</th>
                                        <th class="p-4">Jenis</th>
                                        <th class="p-4 whitespace-nowrap">Tanggal</th>
                                        <th class="p-4">Keterangan</th>
                                        <th class="p-4">Bukti</th>
                                        <th class="p-4">Status</th>
                                        <th class="p-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @forelse($izins as $iz)
                                        <tr class="align-top hover:bg-slate-50/90 transition">
                                            <td class="p-4 font-bold text-slate-800">{{ $iz->user->name ?? '-' }}</td>
                                            <td class="p-4">
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-bold text-[11px] uppercase">{{ $iz->jenis }}</span>
                                            </td>
                                            <td class="p-4 whitespace-nowrap font-medium">{{ \Carbon\Carbon::parse($iz->tanggal ?? $iz->tanggal_mulai)->format('d/m/Y') }}</td>
                                            <td class="p-4 max-w-xs leading-relaxed">{{ $iz->keterangan }}</td>
                                            <td class="p-4 whitespace-nowrap">
                                                <button type="button"
                                                        class="btn-bukti px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-[#0D6353] border border-emerald-200 rounded-xl font-bold inline-flex items-center gap-1.5 transition"
                                                        data-url="{{ route('admin.izin.bukti', $iz->id) }}"
                                                        data-nama="{{ $iz->bukti_nama }}">
                                                    <i class="fa-solid fa-paperclip"></i> Lihat
                                                </button>
                                            </td>
                                            <td class="p-4">
                                                @if($iz->status === 'disetujui')
                                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">Disetujui</span>
                                                @elseif($iz->status === 'ditolak')
                                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 font-extrabold text-[10px]">Ditolak</span>
                                                    @if($iz->catatan_admin)
                                                        <div class="text-[11px] text-slate-500 mt-1.5 max-w-[180px] leading-snug">{{ $iz->catatan_admin }}</div>
                                                    @endif
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-extrabold text-[10px]">Menunggu</span>
                                                @endif
                                            </td>
                                            <td class="p-4 text-center whitespace-nowrap">
                                                @if($iz->status === 'menunggu')
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <form id="form-setujui-{{ $iz->id }}" action="{{ route('admin.izin.setujui', $iz->id) }}" method="POST" class="inline">
                                                            @csrf @method('PATCH')
                                                            <button type="button"
                                                                    data-form="form-setujui-{{ $iz->id }}"
                                                                    data-guru="{{ $iz->user->name ?? 'Guru' }}"
                                                                    onclick="confirmSetujuiIzin(this)"
                                                                    class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-xs transition">
                                                                Setujui
                                                            </button>
                                                        </form>
                                                        <button type="button"
                                                                class="btn-tolak px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold shadow-xs transition"
                                                                data-url="{{ route('admin.izin.tolak', $iz->id) }}"
                                                                data-guru="{{ $iz->user->name ?? '-' }}">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 font-semibold">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-8 text-center text-slate-400 italic">
                                                Tidak ada pengajuan pada kategori ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($izins->hasPages())
                            <div class="p-4 sm:px-6 border-t border-slate-100">
                                {{ $izins->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- MODAL BUKTI -->
    <div id="modalBukti" class="hidden fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl h-[85vh] flex flex-col overflow-hidden border border-slate-100">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-[#0D6353] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h3 id="buktiNama" class="text-sm font-bold text-slate-900 truncate"></h3>
                </div>
                <button type="button" id="buktiTutup" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold shrink-0 transition">
                    <i class="fa-solid fa-xmark mr-1"></i> Tutup
                </button>
            </div>
            <iframe id="buktiFrame" title="Bukti" class="flex-1 w-full border-0 bg-slate-100"></iframe>
        </div>
    </div>

    <!-- MODAL TOLAK -->
    <div id="modalTolak" class="hidden fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-6 shadow-2xl w-full max-w-sm border border-slate-100">
            <div class="flex items-center gap-2.5 mb-2">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tolak Pengajuan</h3>
                    <p id="tolakGuru" class="text-xs font-semibold text-slate-500"></p>
                </div>
            </div>
            <form id="formTolak" method="POST" class="space-y-3.5 pt-2 border-t border-slate-100">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Catatan / Alasan Penolakan</label>
                    <textarea name="catatan_admin" rows="3" maxlength="500" placeholder="Tuliskan alasan penolakan (opsional)..."
                              class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-rose-500 focus:bg-white transition"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="tolakBatal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow transition">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalBukti = document.getElementById('modalBukti');
        const buktiFrame = document.getElementById('buktiFrame');

        document.querySelectorAll('.btn-bukti').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('buktiNama').textContent = btn.dataset.nama || 'Lampiran Bukti';
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

        const modalTolak = document.getElementById('modalTolak');
        document.querySelectorAll('.btn-tolak').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('formTolak').action = btn.dataset.url;
                document.getElementById('tolakGuru').textContent = 'Guru: ' + btn.dataset.guru;
                modalTolak.classList.remove('hidden');
            });
        });
        const tutupTolak = () => modalTolak.classList.add('hidden');
        document.getElementById('tolakBatal').addEventListener('click', tutupTolak);
        modalTolak.addEventListener('click', function (e) { if (e.target === modalTolak) tutupTolak(); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                tutupBukti();
                tutupTolak();
            }
        });

        function confirmSetujuiIzin(btn) {
            const formId = btn.getAttribute('data-form');
            const guru = btn.getAttribute('data-guru');

            Swal.fire({
                title: 'Setujui Izin Guru?',
                text: `Setujui pengajuan izin untuk ${guru}? Presensi guru pada tanggal tersebut akan otomatis terisi dan terkunci.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#19A389',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>