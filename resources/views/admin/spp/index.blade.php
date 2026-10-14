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
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN / No. Kuitansi..." class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-700">
                            
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
                                    <th class="p-3">No. Kuitansi</th>
                                    <th class="p-3">NISN</th>
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Periode</th>
                                    <th class="p-3">Nominal</th>
                                    <th class="p-3">Metode</th>
                                    <th class="p-3">Tanggal Bayar</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Petugas</th>
                                    <th class="p-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($rekapSpp as $index => $item)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3">{{ $rekapSpp->firstItem() + $index }}</td>
                                        <td class="p-3 font-mono text-[11px] font-bold text-slate-800 whitespace-nowrap">{{ $item->no_transaksi ?? '-' }}</td>
                                        <td class="p-3">{{ $item->siswa->nisn ?? '-' }}</td>
                                        <td class="p-3 font-semibold text-slate-900">{{ $item->siswa->nama ?? '-' }}</td>
                                        <td class="p-3">{{ $item->bulan }} {{ $item->tahun }}</td>
                                        <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 bg-slate-100 rounded-md text-[10px] font-bold border border-slate-200">
                                                {{ $item->metode_pembayaran }}
                                            </span>
                                        </td>
                                        <td class="p-3">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}</td>
                                        <td class="p-3">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-slate-500">{{ $item->admin->name ?? '-' }}</td>
                                        <td class="p-3">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('admin.spp.kuitansi', $item->id) }}" target="_blank" title="Cetak Kuitansi"
                                                   class="px-2.5 py-1.5 bg-teal-50 text-[#138A73] rounded-lg hover:bg-teal-100 transition">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                                <button type="button" title="Koreksi Data"
                                                        class="btn-edit-spp px-2.5 py-1.5 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition"
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
                                                    <button type="submit" title="Hapus Data" class="px-2.5 py-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="p-6 text-center text-slate-400">Belum ada data pembayaran SPP.</td>
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

    <!-- MODAL KOREKSI SPP -->
    <div id="modalEditSpp" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl w-full max-w-lg">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Koreksi Data SPP</h3>
            <p class="text-xs text-slate-500 mb-4">No. Kuitansi: <span id="editNo" class="font-mono font-bold text-slate-800"></span></p>

            <form id="formEditSpp" method="POST" class="space-y-3 text-xs" data-url="{{ route('admin.spp.update', '__ID__') }}">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-medium text-slate-500 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" id="editNominal" required min="0" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                </div>
                <div>
                    <label class="block font-medium text-slate-500 mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran" id="editMetode" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="QRIS">QRIS</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-slate-500 mb-1">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" id="editKeterangan" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]">
                </div>
                <div>
                    <label class="block font-medium text-slate-500 mb-1">Alasan Perubahan <span class="text-rose-500">*</span></label>
                    <textarea name="alasan" rows="2" required minlength="5" maxlength="500" placeholder="Contoh: Salah input nominal" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Perubahan dan alasan ini akan tercatat permanen di Audit Trail.</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" id="btnBatalEdit" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#24B9A1] text-white rounded-xl font-semibold hover:bg-[#1DA38D] transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // ===== HAPUS SPP (wajib alasan) =====
            document.querySelectorAll('.form-hapus-spp').forEach(f => {
                f.addEventListener('submit', function (e) {
                    const alasan = prompt('Hapus data SPP ' + f.dataset.no + '?\nTuliskan alasan penghapusan (minimal 5 karakter):');
                    if (alasan === null || alasan.trim().length < 5) {
                        e.preventDefault();
                        if (alasan !== null) alert('Alasan penghapusan minimal 5 karakter.');
                        return;
                    }
                    f.querySelector('[name=alasan]').value = alasan.trim();
                });
            });

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