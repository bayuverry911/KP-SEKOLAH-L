<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan & Rekap SPP - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PENCATATAN SPP</h1>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-8 mt-4">
                
                @if(session('success'))
                    <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>{{ session('error') ?? $errors->first() }}</span>
                    </div>
                @endif

                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-wallet text-[#24B9A1]"></i> Input Pembayaran SPP
                    </h2>

                    <form action="{{ route('admin.spp.store') }}" method="POST" class="space-y-4" id="formSpp">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
                            
                            <div class="relative lg:col-span-2">
                                <label class="block font-medium text-slate-500 mb-1">Cari / Ketik Nama Siswa</label>
                                <input type="text" id="search_siswa" list="daftar_siswa" placeholder="Ketik NISN atau Nama..." required autocomplete="off"
                                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                
                                <datalist id="daftar_siswa">
                                    @foreach($siswas as $siswa)
                                        <option data-id="{{ $siswa->id }}" value="{{ $siswa->nisn }} - {{ $siswa->nama }}"></option>
                                    @endforeach
                                </datalist>
                                <input type="hidden" name="siswa_id" id="siswa_id_hidden" required>
                            </div>

                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Untuk Bulan</label>
                                <select name="bulan" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                    @foreach($daftarBulan as $bulan)
                                        <option value="{{ $bulan }}" {{ Carbon\Carbon::now()->translatedFormat('F') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Tahun</label>
                                <input type="number" name="tahun" value="{{ date('Y') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                            </div>

                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Nominal (Rp)</label>
                                <input type="number" name="nominal" placeholder="Contoh: 150000" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <!-- INPUT METODE PEMBAYARAN -->
                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                            
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan (Opsional)</label>
                                <input type="text" name="keterangan" placeholder="Contoh: Dibayar oleh wali murid" class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                            </div>
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-[#24B9A1] hover:bg-[#1DA38D] text-white font-semibold rounded-2xl text-xs shadow-lg transition mt-2">
                            <i class="fa-solid fa-save mr-1"></i> Simpan Pembayaran
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-[#24B9A1]"></i> Rekap Pembayaran SPP
                        </h2>

                        <form method="GET" action="{{ route('admin.spp.index') }}" class="flex flex-wrap items-center gap-2 text-xs w-full sm:w-auto">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN..." class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                            
                            <select name="bulan" class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                                <option value="">Semua Bulan</option>
                                @foreach($daftarBulan as $b)
                                    <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="p-2.5 bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition">
                                <i class="fa-solid fa-filter"></i>
                            </button>
                            @if(request('search') || request('bulan'))
                                <a href="{{ route('admin.spp.index') }}" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-100 transition" title="Reset Filter">
                                    <i class="fa-solid fa-rotate-right"></i>
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                                    <th class="p-3">#</th>
                                    <th class="p-3">NISN</th>
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Periode</th>
                                    <th class="p-3">Nominal</th>
                                    <th class="p-3">Metode</th>
                                    <th class="p-3">Tanggal Bayar</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($rekapSpp as $index => $item)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3">{{ $rekapSpp->firstItem() + $index }}</td>
                                        <td class="p-3">{{ $item->siswa->nisn ?? '-' }}</td>
                                        <td class="p-3 font-semibold text-slate-900">{{ $item->siswa->nama ?? '-' }}</td>
                                        <td class="p-3">{{ $item->bulan }} {{ $item->tahun }}</td>
                                        <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 bg-slate-100 rounded-md text-[10px] font-bold border border-slate-200">
                                                {{ $item->metode_pembayaran }}
                                            </span>
                                        </td>
                                        <td class="p-3">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->translatedFormat('d M Y, H:i') }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-slate-500">{{ $item->admin->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-6 text-center text-slate-400">Belum ada data pembayaran SPP.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $rekapSpp->links() }}
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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