<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan & Rekap SPP - SMP Djojoredjo</title>
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
                        KEUANGAN • PENCATATAN SPP
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60">
                        <i class="fa-regular fa-calendar text-[#19A389]"></i>
                        <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                    @if(Route::has('admin.spp.audit'))
                        <a href="{{ route('admin.spp.audit') }}"
                           class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition">
                            <i class="fa-solid fa-clock-rotate-left text-[#19A389]"></i>
                            <span>Audit Trail</span>
                        </a>
                    @endif
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Administrasi Keuangan Siswa</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Pencatatan & Rekap SPP</h2>
                            <p class="text-xs text-white/80 mt-1">Input pembayaran bulanan siswa, cetak kuitansi otomatis, dan kelola riwayat transaksi SPP</p>
                        </div>
                        <div class="bg-white/15 backdrop-blur-sm border border-white/20 px-4 py-2.5 rounded-2xl text-xs font-semibold inline-flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <i class="fa-solid fa-receipt"></i>
                            <span>{{ $rekapSpp->total() }} Transaksi</span>
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

                    <!-- CARD FORM INPUT PEMBAYARAN SPP -->
                    <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-xl border border-slate-100">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-[#0D6353] flex items-center justify-center text-base shrink-0">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Input Pembayaran SPP</h3>
                                <p class="text-[11px] sm:text-xs text-slate-400">Pilih siswa dari daftar pencarian lalu lengkapi rincian pembayaran</p>
                            </div>
                        </div>

                        <form action="{{ route('admin.spp.store') }}" method="POST" class="space-y-4" id="formSpp">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div class="relative lg:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Cari / Ketik Nama Siswa</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-user-graduate text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                        <input type="text" id="search_siswa" list="daftar_siswa" placeholder="Ketik NISN atau Nama..." required autocomplete="off"
                                               class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    </div>

                                    <datalist id="daftar_siswa">
                                        @foreach($siswas as $siswa)
                                            <option data-id="{{ $siswa->id }}" value="{{ $siswa->nisn }} - {{ $siswa->nama }}"></option>
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="siswa_id" id="siswa_id_hidden" required>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Untuk Bulan</label>
                                    <select name="bulan" required
                                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        @foreach($daftarBulan as $bulan)
                                            <option value="{{ $bulan }}" {{ Carbon\Carbon::now()->translatedFormat('F') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Tahun</label>
                                    <input type="number" name="tahun" value="{{ date('Y') }}" required
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Nominal (Rp)</label>
                                    <input type="number" name="nominal" placeholder="Contoh: 150000" required min="0"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" required
                                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                        <option value="Tunai">Tunai</option>
                                        <option value="Transfer Bank">Transfer Bank</option>
                                        <option value="QRIS">QRIS</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Keterangan (Opsional)</label>
                                    <input type="text" name="keterangan" placeholder="Contoh: Dibayar oleh wali murid"
                                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <button type="submit"
                                        class="w-full sm:w-auto px-6 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Simpan Pembayaran</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- CARD REKAP PEMBAYARAN SPP -->
                    <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                        <!-- Header & Filter Rekap -->
                        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-[#19A389] flex items-center justify-center text-base shrink-0">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Rekap Pembayaran SPP</h3>
                                    <p class="text-[11px] sm:text-xs text-slate-400">Daftar transaksi pembayaran masuk beserta cetak kuitansi & koreksi</p>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('admin.spp.index') }}" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                                <div class="relative flex-1 sm:w-64">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NISN / No. Kuitansi..."
                                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                </div>

                                <select name="bulan"
                                        class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-700 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">Semua Bulan</option>
                                    @foreach($daftarBulan as $b)
                                        <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                        class="px-4 py-2.5 bg-[#19A389] hover:bg-[#138A73] text-white rounded-xl text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-filter"></i>
                                    <span>Filter</span>
                                </button>
                                @if(request('search') || request('bulan'))
                                    <a href="{{ route('admin.spp.index') }}"
                                       class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </form>
                        </div>

                        <!-- TAMPILAN HP (< 768px) -->
                        <div class="block md:hidden divide-y divide-slate-100">
                            @forelse($rekapSpp as $item)
                                <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="font-mono text-[11px] font-extrabold text-[#138A73] bg-teal-50 px-2 py-0.5 rounded-md border border-teal-100">
                                                {{ $item->no_transaksi ?? '-' }}
                                            </span>
                                            <p class="font-bold text-sm text-slate-900 mt-1 truncate">{{ $item->siswa->nama ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-500">NISN: {{ $item->siswa->nisn ?? '-' }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200 shrink-0">
                                            {{ $item->status }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-2xl border border-slate-100 text-xs">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Periode</span>
                                            <span class="font-bold text-slate-800">{{ $item->bulan }} {{ $item->tahun }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Nominal</span>
                                            <span class="font-extrabold text-emerald-600">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Metode</span>
                                            <span class="font-semibold text-slate-700">{{ $item->metode_pembayaran }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Waktu Bayar</span>
                                            <span class="font-medium text-slate-600">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between gap-2 pt-1">
                                        <span class="text-[11px] text-slate-400">Petugas: <b class="text-slate-600">{{ $item->admin->name ?? '-' }}</b></span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" title="Lihat / Cetak Kuitansi"
                                                    class="btn-kuitansi px-3 py-1.5 bg-teal-50 text-[#138A73] border border-teal-100 rounded-lg text-xs font-bold hover:bg-teal-100 transition inline-flex items-center gap-1"
                                                    data-url="{{ route('admin.spp.kuitansi', ['spp' => $item->id, 'embed' => 1]) }}"
                                                    data-no="{{ $item->no_transaksi }}">
                                                <i class="fa-solid fa-print"></i> Kuitansi
                                            </button>
                                            <button type="button" title="Koreksi Data"
                                                    class="btn-edit-spp p-2 bg-amber-50 text-amber-600 border border-amber-100 rounded-lg hover:bg-amber-100 transition"
                                                    data-id="{{ $item->id }}"
                                                    data-no="{{ $item->no_transaksi }}"
                                                    data-nominal="{{ (float) $item->nominal }}"
                                                    data-metode="{{ $item->metode_pembayaran }}"
                                                    data-keterangan="{{ $item->keterangan }}">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <form action="{{ route('admin.spp.destroy', $item->id) }}" method="POST" class="form-hapus-spp" data-no="{{ $item->no_transaksi }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="alasan" value="">
                                                <button type="submit" title="Hapus Data" class="p-2 bg-rose-50 text-rose-600 border border-rose-100 rounded-lg hover:bg-rose-100 transition">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 italic text-xs">
                                    <i class="fa-regular fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                    Belum ada data pembayaran SPP.
                                </div>
                            @endforelse
                        </div>

                        <!-- TAMPILAN TABLET & DESKTOP (>= 768px) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#19A389] text-white font-bold uppercase tracking-wider text-[11px]">
                                        <th class="p-3.5 text-center w-12">No</th>
                                        <th class="p-3.5 whitespace-nowrap">No. Kuitansi</th>
                                        <th class="p-3.5">NISN</th>
                                        <th class="p-3.5">Nama Siswa</th>
                                        <th class="p-3.5 whitespace-nowrap">Periode</th>
                                        <th class="p-3.5 whitespace-nowrap">Nominal</th>
                                        <th class="p-3.5">Metode</th>
                                        <th class="p-3.5 whitespace-nowrap">Tanggal Bayar</th>
                                        <th class="p-3.5 text-center">Status</th>
                                        <th class="p-3.5">Petugas</th>
                                        <th class="p-3.5 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($rekapSpp as $index => $item)
                                        <tr class="hover:bg-teal-50/40 transition">
                                            <td class="p-3.5 text-center font-semibold text-slate-400">{{ $rekapSpp->firstItem() + $index }}</td>
                                            <td class="p-3.5 font-mono text-[11px] font-bold text-[#138A73] whitespace-nowrap">{{ $item->no_transaksi ?? '-' }}</td>
                                            <td class="p-3.5 font-mono text-slate-500">{{ $item->siswa->nisn ?? '-' }}</td>
                                            <td class="p-3.5 font-bold text-slate-900">{{ $item->siswa->nama ?? '-' }}</td>
                                            <td class="p-3.5 font-medium whitespace-nowrap">{{ $item->bulan }} {{ $item->tahun }}</td>
                                            <td class="p-3.5 font-extrabold text-emerald-600 whitespace-nowrap">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                            <td class="p-3.5 whitespace-nowrap">
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold border border-slate-200">
                                                    {{ $item->metode_pembayaran }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 text-slate-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}</td>
                                            <td class="p-3.5 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                    {{ $item->status }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 text-slate-500">{{ $item->admin->name ?? '-' }}</td>
                                            <td class="p-3.5">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <button type="button" title="Lihat / Cetak Kuitansi"
                                                            class="btn-kuitansi p-2 bg-teal-50 text-[#138A73] rounded-lg hover:bg-teal-100 transition"
                                                            data-url="{{ route('admin.spp.kuitansi', ['spp' => $item->id, 'embed' => 1]) }}"
                                                            data-no="{{ $item->no_transaksi }}">
                                                        <i class="fa-solid fa-print"></i>
                                                    </button>
                                                    <button type="button" title="Koreksi Data"
                                                            class="btn-edit-spp p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition"
                                                            data-id="{{ $item->id }}"
                                                            data-no="{{ $item->no_transaksi }}"
                                                            data-nominal="{{ (float) $item->nominal }}"
                                                            data-metode="{{ $item->metode_pembayaran }}"
                                                            data-keterangan="{{ $item->keterangan }}">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    <form action="{{ route('admin.spp.destroy', $item->id) }}" method="POST" class="form-hapus-spp" data-no="{{ $item->no_transaksi }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="alasan" value="">
                                                        <button type="submit" title="Hapus Data" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="p-8 text-center text-slate-400 italic">Belum ada data pembayaran SPP.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($rekapSpp->hasPages())
                            <div class="p-4 sm:px-6 border-t border-slate-100">
                                {{ $rekapSpp->links() }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- MODAL KUITANSI (lihat / cetak tanpa pindah tab) -->
    <div id="modalKuitansi" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-[1px] flex items-center justify-center p-3 sm:p-6"
         data-otomatis-url="{{ session('kuitansi_id') ? route('admin.spp.kuitansi', ['spp' => session('kuitansi_id'), 'embed' => 1]) : '' }}"
         data-otomatis-no="{{ session('kuitansi_no') }}">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl h-[90vh] flex flex-col overflow-hidden border border-slate-100">
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-[#0D6353] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Kuitansi Pembayaran SPP</h3>
                        <p class="text-[11px] text-slate-500">No. <span id="kuitansiNo" class="font-mono font-bold text-[#138A73]"></span></p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnCetakKuitansi" class="px-4 py-2 bg-[#19A389] hover:bg-[#138A73] text-white text-xs font-bold rounded-xl shadow transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak</span>
                    </button>
                    <button type="button" id="btnTutupKuitansi" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
            <div class="relative flex-1 bg-slate-100">
                <div id="kuitansiLoading" class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-slate-500">
                    <i class="fa-solid fa-spinner fa-spin text-[#19A389] mr-2"></i> Memuat kuitansi...
                </div>
                <iframe id="frameKuitansi" title="Kuitansi SPP" class="relative w-full h-full border-0 bg-transparent"></iframe>
            </div>
        </div>
    </div>

    <!-- MODAL KOREKSI SPP -->
    <div id="modalEditSpp" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-[1px] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-2xl w-full max-w-lg border border-slate-100">
            <div class="flex justify-between items-start gap-3 mb-4 pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Koreksi Data SPP</h3>
                        <p class="text-[11px] text-slate-500">No. Kuitansi: <span id="editNo" class="font-mono font-bold text-slate-800"></span></p>
                    </div>
                </div>
                <button type="button" id="btnXEdit" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formEditSpp" method="POST" class="space-y-3.5 text-xs" data-url="{{ route('admin.spp.update', '__ID__') }}">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Nominal (Rp)</label>
                    <input type="number" name="nominal" id="editNominal" required min="0"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="editMetode" required
                            class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" id="editKeterangan"
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-1.5">Alasan Perubahan <span class="text-rose-500">*</span></label>
                    <textarea name="alasan" rows="2" required minlength="5" maxlength="500" placeholder="Contoh: Salah input nominal"
                              class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-shield-halved text-[#19A389]"></i>
                        <span>Perubahan dan alasan ini akan tercatat permanen di Audit Trail.</span>
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" id="btnBatalEdit" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#19A389] text-white rounded-xl font-extrabold hover:bg-[#138A73] shadow-md transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== KUITANSI DI MODAL =====
            const modalKuitansi = document.getElementById('modalKuitansi');
            const frameKuitansi = document.getElementById('frameKuitansi');
            const loadingKuitansi = document.getElementById('kuitansiLoading');

            function bukaKuitansi(url, nomor) {
                document.getElementById('kuitansiNo').textContent = nomor || '';
                loadingKuitansi.classList.remove('hidden');
                frameKuitansi.classList.add('invisible');
                frameKuitansi.src = url;
                modalKuitansi.classList.remove('hidden');
            }
            function tutupKuitansi() {
                modalKuitansi.classList.add('hidden');
                frameKuitansi.src = 'about:blank';
            }

            frameKuitansi.addEventListener('load', () => {
                if (frameKuitansi.src && frameKuitansi.src !== 'about:blank') {
                    loadingKuitansi.classList.add('hidden');
                    frameKuitansi.classList.remove('invisible');
                }
            });
            document.querySelectorAll('.btn-kuitansi').forEach(btn => {
                btn.addEventListener('click', () => bukaKuitansi(btn.dataset.url, btn.dataset.no));
            });
            document.getElementById('btnTutupKuitansi').addEventListener('click', tutupKuitansi);
            document.getElementById('btnCetakKuitansi').addEventListener('click', () => {
                frameKuitansi.contentWindow.focus();
                frameKuitansi.contentWindow.print();
            });
            modalKuitansi.addEventListener('click', e => { if (e.target === modalKuitansi) tutupKuitansi(); });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    if (!modalKuitansi.classList.contains('hidden')) tutupKuitansi();
                    if (!modal.classList.contains('hidden')) modal.classList.add('hidden');
                }
            });

            // Setelah simpan pembayaran, kuitansi langsung dibuka di modal
            const kuitansiOtomatis = modalKuitansi.dataset.otomatisUrl;
            if (kuitansiOtomatis) {
                bukaKuitansi(kuitansiOtomatis, modalKuitansi.dataset.otomatisNo);
            }

            // ===== KOREKSI SPP =====
            const modal = document.getElementById('modalEditSpp');
            const formEdit = document.getElementById('formEditSpp');
            const urlUpdate = formEdit.dataset.url;

            document.querySelectorAll('.btn-edit-spp').forEach(btn => {
                btn.addEventListener('click', () => {
                    formEdit.action = urlUpdate.replace('__ID__', btn.dataset.id);
                    document.getElementById('editNo').textContent = btn.dataset.no;
                    document.getElementById('editNominal').value = btn.dataset.nominal;
                    document.getElementById('editMetode').value = btn.dataset.metode;
                    document.getElementById('editKeterangan').value = btn.dataset.keterangan || '';
                    formEdit.querySelector('[name=alasan]').value = '';
                    modal.classList.remove('hidden');
                });
            });
            document.getElementById('btnBatalEdit').addEventListener('click', () => modal.classList.add('hidden'));
            document.getElementById('btnXEdit').addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });

            // ===== HAPUS SPP (wajib alasan) =====
            document.querySelectorAll('.form-hapus-spp').forEach(f => {
                f.addEventListener('submit', function (e) {
                    if (f.dataset.alasanOk === '1') return;
                    e.preventDefault();
                    if (typeof uiPrompt === 'function') {
                        uiPrompt({
                            judul: 'Hapus Data SPP',
                            pesan: 'Hapus data SPP ' + f.dataset.no + '? Tuliskan alasan penghapusan.',
                            placeholder: 'Alasan penghapusan (minimal 5 karakter)',
                            min: 5, ok: 'Hapus', varian: 'bad'
                        }, function (alasan) {
                            f.querySelector('[name=alasan]').value = alasan;
                            f.dataset.alasanOk = '1';
                            f.submit();
                        });
                    } else {
                        const alasan = prompt('Hapus data SPP ' + f.dataset.no + '? Tuliskan alasan penghapusan (minimal 5 karakter):');
                        if (alasan && alasan.trim().length >= 5) {
                            f.querySelector('[name=alasan]').value = alasan.trim();
                            f.dataset.alasanOk = '1';
                            f.submit();
                        }
                    }
                });
            });

            // ===== AUTOCOMPLETE SISWA =====
            const searchInput = document.getElementById('search_siswa');
            const hiddenInput = document.getElementById('siswa_id_hidden');
            const datalistOptions = document.getElementById('daftar_siswa').options;

            searchInput.addEventListener('input', function() {
                let selectedValue = this.value;
                hiddenInput.value = '';

                for (let i = 0; i < datalistOptions.length; i++) {
                    if (datalistOptions[i].value === selectedValue) {
                        hiddenInput.value = datalistOptions[i].getAttribute('data-id');
                        break;
                    }
                }
            });

            document.getElementById('formSpp').addEventListener('submit', function(e) {
                if (!hiddenInput.value) {
                    e.preventDefault();
                    alert("Nama siswa tidak valid. Pastikan Anda memilih/klik nama siswa dari daftar yang muncul saat mengetik.");
                    searchInput.focus();
                }
            });
        });
    </script>
</body>
</html>