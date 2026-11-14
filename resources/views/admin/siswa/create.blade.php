<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa - SMP Djojoredjo</title>
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
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden"
      data-default-tab="{{ ($errors->has('file_excel') || session('tab') === 'excel' || session('toast_duplikat')) ? 'excel' : 'manual' }}">

    <!-- TOAST PEMBERITAHUAN DUPLIKAT NISN & NAMA -->
    @if (session('toast_duplikat'))
        @php $toast = session('toast_duplikat'); @endphp
        <div id="toastDuplikat"
             class="fixed top-4 right-4 left-4 sm:left-auto z-50 sm:w-full sm:max-w-md bg-white border-l-4 border-amber-500 rounded-2xl shadow-2xl p-4 transition-all duration-300">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-extrabold text-slate-900">{{ $toast['judul'] }}</h4>
                        <button type="button" onclick="tutupToast()" class="text-slate-400 hover:text-slate-700 text-xs p-1">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $toast['pesan'] }}</p>
                    <ul class="mt-2 max-h-36 overflow-y-auto space-y-1 bg-amber-50/80 border border-amber-200/80 rounded-xl p-2.5 text-[11px] text-amber-950 font-medium">
                        @foreach ($toast['items'] as $item)
                            <li class="flex items-start gap-1.5">
                                <span class="text-amber-600 font-bold">•</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

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
                        DATA SISWA • TAMBAH DATA SISWA
                    </h1>
                </div>

                <a href="{{ route('admin.siswa.index') }}"
                   class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl shadow-sm border border-slate-200/60 transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-[#19A389]"></i>
                    <span>Kembali</span>
                </a>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-2xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">Master Data Peserta Didik</p>
                            <h2 class="text-xl sm:text-2xl font-extrabold mt-0.5">Tambah Data Siswa</h2>
                            <p class="text-xs text-white/80 mt-1">Input satu per satu secara manual atau unggah massal melalui file Excel</p>
                        </div>
                        <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                    </div>

                    <!-- FORM & IMPORT CARD -->
                    <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-xl border border-slate-100 space-y-5">

                        <!-- TAB SWITCHER: MANUAL VS EXCEL -->
                        <div class="bg-slate-100 p-1.5 rounded-2xl grid grid-cols-2 gap-1.5 text-xs font-bold">
                            <button type="button" id="tabBtnManual" onclick="switchTab('manual')"
                                    class="py-2.5 px-3 rounded-xl bg-white text-[#138A73] shadow-sm transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>Input Manual</span>
                            </button>
                            <button type="button" id="tabBtnExcel" onclick="switchTab('excel')"
                                    class="py-2.5 px-3 rounded-xl text-slate-500 hover:text-slate-800 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                                <span>Import Excel</span>
                            </button>
                        </div>

                        @if (session('success'))
                            <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-semibold">
                                <div class="flex items-start gap-2.5">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                                <a href="{{ route('admin.siswa.index') }}" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-[11px] font-bold text-center shrink-0 transition">
                                    Lihat Daftar Siswa
                                </a>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl flex items-start gap-2.5 font-semibold">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base mt-0.5 shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-2xl">
                                <ul class="list-disc pl-4 space-y-1 font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- PANEL 1: FORM INPUT MANUAL -->
                        <form id="panelManual" action="{{ route('admin.siswa.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    NISN <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nisn" value="{{ old('nisn') }}" required placeholder="Contoh: 0051234567"
                                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-mono text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Nama Lengkap Siswa <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap"
                                       class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Kelas <span class="text-rose-500">*</span>
                                </label>
                                <select name="kelas" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 font-bold focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelases as $kelas)
                                        <option value="{{ $kelas->nama_kelas }}" {{ old('kelas') == $kelas->nama_kelas ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                    Jenis Kelamin <span class="text-rose-500">*</span>
                                </label>
                                <select name="jenis_kelamin" required
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs text-slate-700 font-semibold focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                        class="w-full py-3.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl text-xs shadow-md transition inline-flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Simpan Data Siswa</span>
                                </button>
                            </div>
                        </form>

                        <!-- PANEL 2: FORM IMPORT EXCEL -->
                        <div id="panelExcel" class="hidden space-y-5 text-xs">
                            <div class="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <p class="font-bold text-emerald-900">
                                        <i class="fa-solid fa-circle-info mr-1 text-[#19A389]"></i> Panduan Format File Excel
                                    </p>
                                    <a href="{{ route('admin.siswa.template') }}"
                                       class="px-3.5 py-2 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-download"></i>
                                        <span>Unduh Template (.xlsx)</span>
                                    </a>
                                </div>
                                <p class="text-[11px] text-emerald-900 leading-relaxed">
                                    Baris pertama file Excel wajib berisi judul kolom: <b>NISN</b>, <b>Nama</b>, <b>Kelas</b>, dan <b>Jenis Kelamin</b> (<code>Laki-laki</code> / <code>Perempuan</code> atau <code>L</code> / <code>P</code>).
                                </p>
                                <div class="overflow-x-auto rounded-xl border border-emerald-200 bg-white">
                                    <table class="w-full text-left text-[11px]">
                                        <thead class="bg-[#0D6353] text-white">
                                            <tr>
                                                <th class="px-3 py-2">NISN</th>
                                                <th class="px-3 py-2">Nama</th>
                                                <th class="px-3 py-2">Kelas</th>
                                                <th class="px-3 py-2">Jenis Kelamin</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-600 font-mono">
                                            <tr>
                                                <td class="px-3 py-2">0051234567</td>
                                                <td class="px-3 py-2">Ahmad Fauzan</td>
                                                <td class="px-3 py-2">{{ $kelases->first()->nama_kelas ?? '7A' }}</td>
                                                <td class="px-3 py-2">Laki-laki</td>
                                            </tr>
                                            <tr>
                                                <td class="px-3 py-2">0057654321</td>
                                                <td class="px-3 py-2">Siti Aisyah</td>
                                                <td class="px-3 py-2">{{ $kelases->first()->nama_kelas ?? '7A' }}</td>
                                                <td class="px-3 py-2">Perempuan</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <form id="formImportExcel" action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                                        Pilih File Excel (.xlsx atau .csv) <span class="text-rose-500">*</span>
                                    </label>

                                    <input type="file" name="file_excel" id="file_excel" accept=".xlsx,.csv" required class="hidden">

                                    <!-- KOTAK BELUM PILIH FILE -->
                                    <div id="dropzone_kosong" onclick="document.getElementById('file_excel').click()"
                                         class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 hover:border-[#19A389] rounded-2xl bg-slate-50 hover:bg-emerald-50/30 cursor-pointer transition text-center">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#19A389] mb-2"></i>
                                        <span class="font-bold text-slate-700">Klik atau seret file Excel ke sini</span>
                                        <span class="text-[11px] text-slate-400 mt-1">Format didukung: .xlsx, .csv (Maksimal 5 MB)</span>
                                    </div>

                                    <!-- KARTU FILE SUDAH DIPILIH -->
                                    <div id="dropzone_terisi" class="hidden p-4 rounded-2xl border-2 border-emerald-400 bg-emerald-50/90 flex items-center justify-between gap-3 shadow-sm">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shrink-0 shadow">
                                                <i class="fa-solid fa-file-excel"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 text-[10px] font-bold">
                                                        <i class="fa-solid fa-check mr-0.5"></i> File Terpilih
                                                    </span>
                                                    <span id="ukuran_file_excel" class="text-[11px] text-emerald-700 font-semibold"></span>
                                                </div>
                                                <p id="nama_file_excel" class="font-bold text-slate-800 text-xs truncate mt-1"></p>
                                                <p class="text-[11px] text-emerald-700 mt-0.5">Klik tombol <b>Upload & Import Data Siswa</b> di bawah untuk memproses.</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button" onclick="document.getElementById('file_excel').click()"
                                                    class="px-2.5 py-1.5 bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg text-[11px] font-bold transition">
                                                Ganti
                                            </button>
                                            <button type="button" onclick="resetFileExcel()"
                                                    class="px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-lg text-[11px] font-bold transition" title="Hapus file">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="btnSubmitExcel"
                                        class="w-full py-3.5 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-xl text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-file-import"></i>
                                    <span id="labelSubmitExcel">Upload & Import Data Siswa</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        function switchTab(tab) {
            const panelManual = document.getElementById('panelManual');
            const panelExcel  = document.getElementById('panelExcel');
            const btnManual   = document.getElementById('tabBtnManual');
            const btnExcel    = document.getElementById('tabBtnExcel');

            if (tab === 'excel') {
                panelManual.classList.add('hidden');
                panelExcel.classList.remove('hidden');
                btnExcel.className  = 'py-2.5 px-3 rounded-xl bg-white text-[#138A73] shadow-sm transition flex items-center justify-center gap-2';
                btnManual.className = 'py-2.5 px-3 rounded-xl text-slate-500 hover:text-slate-800 transition flex items-center justify-center gap-2';
            } else {
                panelExcel.classList.add('hidden');
                panelManual.classList.remove('hidden');
                btnManual.className = 'py-2.5 px-3 rounded-xl bg-white text-[#138A73] shadow-sm transition flex items-center justify-center gap-2';
                btnExcel.className  = 'py-2.5 px-3 rounded-xl text-slate-500 hover:text-slate-800 transition flex items-center justify-center gap-2';
            }
        }

        function tutupToast() {
            const toast = document.getElementById('toastDuplikat');
            if (toast) {
                toast.classList.add('opacity-0', 'translate-x-5');
                setTimeout(() => toast.remove(), 300);
            }
        }

        if (document.getElementById('toastDuplikat')) {
            setTimeout(tutupToast, 10000);
        }

        const inputExcel     = document.getElementById('file_excel');
        const dropzoneKosong = document.getElementById('dropzone_kosong');
        const dropzoneTerisi = document.getElementById('dropzone_terisi');
        const namaFileEl     = document.getElementById('nama_file_excel');
        const ukuranFileEl   = document.getElementById('ukuran_file_excel');

        function formatUkuran(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function tampilkanFileTerpilih(file) {
            if (!file) {
                resetFileExcel();
                return;
            }
            namaFileEl.textContent   = file.name;
            ukuranFileEl.textContent = formatUkuran(file.size);
            dropzoneKosong.classList.add('hidden');
            dropzoneTerisi.classList.remove('hidden');
        }

        function resetFileExcel() {
            inputExcel.value = '';
            dropzoneTerisi.classList.add('hidden');
            dropzoneKosong.classList.remove('hidden');
        }

        inputExcel.addEventListener('change', function () {
            tampilkanFileTerpilih(this.files[0]);
        });

        ['dragenter', 'dragover'].forEach(evt => {
            dropzoneKosong.addEventListener(evt, function (e) {
                e.preventDefault();
                dropzoneKosong.classList.add('border-[#19A389]', 'bg-emerald-50/60');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzoneKosong.addEventListener(evt, function (e) {
                e.preventDefault();
                dropzoneKosong.classList.remove('border-[#19A389]', 'bg-emerald-50/60');
            });
        });

        dropzoneKosong.addEventListener('drop', function (e) {
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                inputExcel.files = files;
                tampilkanFileTerpilih(files[0]);
            }
        });

        document.getElementById('formImportExcel').addEventListener('submit', function () {
            const btn = document.getElementById('btnSubmitExcel');
            const lbl = document.getElementById('labelSubmitExcel');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-wait');
            lbl.textContent = 'Sedang membaca & mengimport data Excel...';
        });

        if (document.body.dataset.defaultTab === 'excel') {
            switchTab('excel');
        }
    </script>
</body>
</html>