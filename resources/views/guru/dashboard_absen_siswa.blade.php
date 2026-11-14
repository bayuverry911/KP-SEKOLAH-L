<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Siswa - SMP Djojoredjo</title>
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
                        PRESENSI SISWA • INPUT KEHADIRAN
                    </h1>
                </div>

                <div class="hidden sm:flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-200/60 shrink-0">
                    <i class="fa-regular fa-calendar text-[#19A389]"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <!-- MAIN BODY CONTENT -->
            <div class="px-4 sm:px-8 pb-8 pt-2 flex-1">
                <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

                    <!-- HERO HEADER CARD -->
                    <div class="rounded-3xl bg-gradient-to-r from-[#19A389] to-[#0D6353] text-white p-5 sm:p-6 shadow-xl flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-[11px] uppercase tracking-widest text-white/75 font-semibold">
                                    @if($utama ?? false)
                                        Absensi Utama Sekolah (Jam Ke-1)
                                    @elseif($kelola ?? false)
                                        Pembaruan Data Kehadiran
                                    @elseif($susulan)
                                        Input Kehadiran Terlewat
                                    @else
                                        Absensi Mata Pelajaran
                                    @endif
                                </p>
                                @if($kelola ?? false)
                                    <span class="px-2 py-0.5 rounded-md bg-sky-400/25 border border-sky-200/40 text-white font-bold text-[10px] uppercase">Kelola Absensi</span>
                                @endif
                                @if($kartu->is_pengganti)
                                    <span class="px-2.5 py-0.5 rounded-md bg-amber-400 text-amber-950 font-extrabold text-[10px]">
                                        Guru Pengganti{{ ($kartu->guru_asli ?? null) ? ' (menggantikan ' . $kartu->guru_asli . ')' : '' }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-xl sm:text-2xl font-extrabold">
                                @if($utama ?? false)
                                    Absensi Utama • Kelas {{ $kartu->kelas }}
                                @else
                                    {{ $kartu->mapel }} • Kelas {{ $kartu->kelas }}
                                @endif
                            </h2>

                            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-white/90 font-medium pt-0.5">
                                @if($utama ?? false)
                                    <span><i class="fa-regular fa-clock mr-1 text-white/75"></i>{{ $kartu->jam_masuk }} WIB</span>
                                @else
                                    <span><i class="fa-solid fa-book-open mr-1 text-white/75"></i>Pertemuan ke-{{ $pertemuan }}</span>
                                    <span class="text-white/40">•</span>
                                    <span><i class="fa-regular fa-clock mr-1 text-white/75"></i>{{ $kartu->jam_masuk }} - {{ $kartu->jam_selesai }} WIB</span>
                                @endif
                                <span class="text-white/40">•</span>
                                <span title="Tanggal terkunci otomatis sesuai jadwal">
                                    <i class="fa-solid fa-lock text-[10px] mr-1 text-white/75"></i>{{ \App\Services\AbsenSiswaService::namaHari($now) }}, {{ $now->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 self-start lg:self-auto shrink-0">
                            <a href="{{ $susulan ? ($kembali ?? route('guru.rekap-siswa')) : route('guru.dashboard') }}"
                               class="bg-white/15 hover:bg-white/25 backdrop-blur-sm border border-white/25 text-white px-4 py-2.5 rounded-2xl text-xs font-extrabold inline-flex items-center gap-2 transition shadow-sm">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>{{ $susulan ? 'Kembali ke Rekap' : 'Kembali ke Dashboard' }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- BANNER INFORMASI & PERINGATAN -->
                    @if(session('error'))
                        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex items-center gap-2.5 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($terkunci ?? false)
                        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-2xl flex items-center gap-2.5 font-semibold shadow-sm">
                            <i class="fa-solid fa-lock text-emerald-600 text-base shrink-0"></i>
                            <span>Absensi Utama diselesaikan oleh: <b>{{ $selesaiOleh }}</b>. Form terkunci dan tidak dapat diubah.</span>
                        </div>
                    @elseif($utama ?? false)
                        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-2xl flex items-start gap-2.5 font-medium shadow-sm">
                            <i class="fa-solid fa-circle-info text-amber-600 text-base mt-0.5 shrink-0"></i>
                            <span>Absensi Utama hanya dilakukan <b>satu kali</b> per hari oleh guru jam ke-1; setelah disimpan, form terkunci. Absensi mata pelajaran tetap diisi masing-masing guru mapel.</span>
                        </div>
                    @endif

                    @if(! ($utama ?? false) && isset($utamaInfo))
                        @if($utamaInfo['ada'])
                            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-2xl flex items-start gap-2.5 font-medium shadow-sm">
                                <i class="fa-solid fa-school-circle-check text-emerald-600 text-base mt-0.5 shrink-0"></i>
                                <span>Absensi Utama (sekolah) kelas ini sudah diisi oleh <b>{{ $utamaInfo['oleh'] ?? '-' }}</b>. Kolom <b>UTAMA</b> menunjukkan kehadiran siswa di jam ke-1 untuk dibandingkan dengan kehadiran di jam Anda.</span>
                            </div>
                        @else
                            <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 text-xs rounded-2xl flex items-start gap-2.5 font-medium shadow-sm">
                                <i class="fa-solid fa-bell text-amber-600 text-base mt-0.5 shrink-0"></i>
                                <span>Absensi Utama (sekolah) kelas ini <b>belum diisi</b>@if($utamaInfo['penanggung']) oleh guru jam ke-1 (<b>{{ $utamaInfo['penanggung'] }}</b>)@endif. Mohon ingatkan agar data kehadiran sekolah lengkap.</span>
                            </div>
                        @endif
                    @endif

                    @if($kelola ?? false)
                        <div class="p-4 bg-sky-50 border border-sky-200 text-sky-900 text-xs rounded-2xl flex items-start gap-2.5 font-medium shadow-sm">
                            <i class="fa-solid fa-pen-to-square text-sky-600 text-base mt-0.5 shrink-0"></i>
                            <span><b>Kelola Absensi.</b> Ubah status siswa yang salah input, lalu simpan. Perubahan hanya memperbarui data yang sudah ada (tidak menambah baris absensi baru).</span>
                        </div>
                    @endif

                    @if($susulan)
                        <div class="p-4 bg-amber-50 border border-amber-300 text-amber-900 text-xs rounded-2xl flex items-start gap-2.5 font-medium shadow-sm">
                            <i class="fa-solid fa-clock-rotate-left text-amber-600 text-base mt-0.5 shrink-0"></i>
                            <span><b>Absen Susulan.</b> Tanggal terkunci sesuai jadwal yang terlewat dan tidak dapat diubah. Tinjau dan salin dari catatan fisik.</span>
                        </div>
                    @endif

                    <!-- CARD UTAMA DAFTAR KEHADIRAN SISWA -->
                    <div class="bg-white rounded-3xl p-4 sm:p-6 shadow-xl border border-slate-100 space-y-5">

                        <!-- TOOLBAR SEARCH & AKSI CEPAT -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 text-xs">
                            <div class="relative w-full lg:max-w-sm">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" id="inputSearchNama" onkeyup="filterSiswaTable()" placeholder="Cari nama siswa..."
                                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-base sm:text-xs font-medium text-slate-800 focus:outline-none focus:border-[#19A389] focus:bg-white transition">
                            </div>

                            @unless($terkunci ?? false)
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" onclick="pilihSemua()"
                                            class="px-4 py-2.5 rounded-xl font-extrabold bg-[#47CB23] hover:bg-[#3bb61c] text-white shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span>Pilih Semua (Hadir)</span>
                                    </button>
                                    <button type="button" onclick="kosongkanSemua()"
                                            class="px-4 py-2.5 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-eraser"></i>
                                        <span>Kosongkan</span>
                                    </button>
                                    <span id="penghitung" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 font-extrabold">Terisi 0 / 0</span>
                                </div>
                            @endunless
                        </div>

                        <!-- FORM SIMPAN BATCH PRESENSI -->
                        <form action="{{ $aksi }}" method="POST" id="formAbsen" onsubmit="return validasiForm(event)">
                            @csrf
                            @if($kelola ?? false) @method('PUT') @endif

                            <div class="overflow-hidden rounded-2xl border border-slate-200">
                                <table class="w-full text-left text-xs block md:table" id="siswaTable">
                                    <thead class="hidden md:table-header-group bg-[#19A389] text-white font-bold uppercase tracking-wider text-[11px]">
                                        <tr>
                                            <th class="p-4 w-1/3">NAMA SISWA</th>
                                            <th class="p-4 w-1/6">KELAS</th>
                                            <th class="p-4 w-1/5">WAKTU</th>
                                            @unless($utama ?? false)
                                                <th class="p-4 text-center" title="Kehadiran di Absensi Utama (jam ke-1)">UTAMA</th>
                                            @endunless
                                            <th class="p-4 text-center">STATUS KEHADIRAN</th>
                                        </tr>
                                    </thead>
                                    <tbody class="block md:table-row-group divide-y divide-slate-100 text-slate-700 font-medium bg-white">

                                        @forelse($siswas as $index => $s)
                                            @php
                                                $siswaId = $s->id ?? ($index + 1);
                                                $namaSiswa = $s->nama ?? $s->name ?? '-';
                                                $kelasSiswa = $s->kelas ?? $kartu->kelas;
                                                $statusSiswa = ($tersimpan ?? [])[$siswaId] ?? '';
                                            @endphp
                                            <tr class="block md:table-row p-4 md:p-0 hover:bg-teal-50/30 transition row-siswa">
                                                <!-- NAMA SISWA (+ INFO RINGKAS DI MOBILE) -->
                                                <td class="block md:table-cell md:p-4 font-bold text-slate-800 nama-cell">
                                                    <div class="flex items-start justify-between gap-2">
                                                        <div>
                                                            <span class="text-sm md:text-xs font-extrabold text-slate-900">{{ $namaSiswa }}</span>
                                                            <div class="md:hidden text-[11px] font-medium text-slate-400 mt-0.5">
                                                                Kelas {{ $kelasSiswa }} • {{ date('d.m.Y H:i') }}
                                                            </div>
                                                        </div>

                                                        @unless($utama ?? false)
                                                            @php
                                                                $hu = ($utamaInfo['status'] ?? [])[$siswaId] ?? null;
                                                                $gu = match($hu) {
                                                                    'H' => 'bg-emerald-500 text-white',
                                                                    'I' => 'bg-sky-500 text-white',
                                                                    'S' => 'bg-amber-400 text-slate-900',
                                                                    'A' => 'bg-rose-500 text-white',
                                                                    default => 'bg-slate-100 text-slate-400',
                                                                };
                                                            @endphp
                                                            <div class="md:hidden inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200/80 px-2 py-1 rounded-lg shrink-0">
                                                                <span class="text-[10px] font-bold text-slate-400 uppercase">Utama:</span>
                                                                <span class="inline-flex w-5 h-5 items-center justify-center rounded text-[10px] font-extrabold {{ $gu }}"
                                                                      title="Absensi Utama: {{ $hu ?? 'belum diisi' }}">{{ $hu ?? '?' }}</span>
                                                            </div>
                                                        @endunless
                                                    </div>
                                                </td>

                                                <!-- KELAS (DESKTOP) -->
                                                <td class="hidden md:table-cell md:p-4 text-slate-600">{{ $kelasSiswa }}</td>

                                                <!-- WAKTU (DESKTOP) -->
                                                <td class="hidden md:table-cell md:p-4 text-slate-500">{{ date('d.m.Y H:i') }}</td>

                                                <!-- UTAMA (DESKTOP) -->
                                                @unless($utama ?? false)
                                                    @php
                                                        $hu = ($utamaInfo['status'] ?? [])[$siswaId] ?? null;
                                                        $gu = match($hu) {
                                                            'H' => 'bg-emerald-500 text-white',
                                                            'I' => 'bg-sky-500 text-white',
                                                            'S' => 'bg-amber-400 text-slate-900',
                                                            'A' => 'bg-rose-500 text-white',
                                                            default => 'bg-slate-100 text-slate-400',
                                                        };
                                                    @endphp
                                                    <td class="hidden md:table-cell md:p-4 text-center">
                                                        <span class="inline-flex w-6 h-6 items-center justify-center rounded-md text-[11px] font-bold {{ $gu }}"
                                                              title="Absensi Utama: {{ $hu ?? 'belum diisi' }}">{{ $hu ?? '?' }}</span>
                                                    </td>
                                                @endunless

                                                <!-- STATUS KEHADIRAN -->
                                                <td class="block md:table-cell mt-3 md:mt-0 md:p-4">
                                                    <div class="flex items-center justify-between md:justify-center gap-2">
                                                        <!-- HIDDEN INPUT VALUE TO POST -->
                                                        <input type="hidden" name="presensi[{{ $siswaId }}]" id="input-status-{{ $siswaId }}" value="{{ $statusSiswa }}">

                                                        <!-- BADGE INDIKATOR (H / S / I / A) -->
                                                        <div id="badge-{{ $siswaId }}"
                                                             class="w-8 h-8 md:w-7 md:h-7 flex items-center justify-center font-bold text-slate-400 text-xs rounded-lg md:rounded-sm bg-slate-100 transition-all duration-200 shrink-0">-</div>

                                                        <!-- TOMBOL PILIHAN STATUS -->
                                                        <div class="grid grid-cols-4 sm:flex items-center gap-1.5 flex-1 md:flex-initial {{ ($terkunci ?? false) ? 'pointer-events-none opacity-90' : '' }}">
                                                            <!-- HADIR (MASUK) -->
                                                            <button type="button" onclick="selectStatus('{{ $siswaId }}', 'MASUK')" id="btn-masuk-{{ $siswaId }}"
                                                                    class="px-2 sm:px-3 py-2 sm:py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-emerald-600 border-emerald-400 hover:bg-emerald-50">
                                                                HADIR
                                                            </button>

                                                            <!-- SAKIT -->
                                                            <button type="button" onclick="selectStatus('{{ $siswaId }}', 'SAKIT')" id="btn-sakit-{{ $siswaId }}"
                                                                    class="px-2 sm:px-3 py-2 sm:py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-purple-600 border-purple-400 hover:bg-purple-50">
                                                                SAKIT
                                                            </button>

                                                            <!-- IZIN -->
                                                            <button type="button" onclick="selectStatus('{{ $siswaId }}', 'IZIN')" id="btn-izin-{{ $siswaId }}"
                                                                    class="px-2 sm:px-3 py-2 sm:py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-amber-600 border-amber-400 hover:bg-amber-50">
                                                                IZIN
                                                            </button>

                                                            <!-- ABSEN / ALPA -->
                                                            <button type="button" onclick="selectStatus('{{ $siswaId }}', 'ABSEN')" id="btn-absen-{{ $siswaId }}"
                                                                    class="px-2 sm:px-3 py-2 sm:py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-rose-600 border-rose-400 hover:bg-rose-50">
                                                                ABSEN
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="block md:table-row">
                                                <td colspan="5" class="block md:table-cell p-8 text-center text-slate-400 italic">
                                                    <i class="fa-solid fa-users-slash text-2xl block mb-2 text-slate-300"></i>
                                                    Belum ada data siswa terdaftar di kelas ini.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                            <!-- TOMBOL SELESAI ABSEN -->
                            <div class="pt-5 flex justify-end {{ ($terkunci ?? false) ? 'hidden' : '' }}">
                                <button type="submit"
                                        class="w-full sm:w-auto px-8 py-3 bg-[#19A389] hover:bg-[#138A73] active:bg-[#0D6353] text-white font-extrabold rounded-2xl shadow-lg transition inline-flex items-center justify-center gap-2 text-xs">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>{{ ($kelola ?? false) ? 'Simpan Perubahan' : 'Selesai Absen' }}</span>
                                </button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </main>
    </div>

    <script>
        const WARNA = {
            MASUK: ['H', 'bg-[#47CB23]', 'bg-[#47CB23] text-white border-[#47CB23] shadow-md'],
            SAKIT: ['S', 'bg-purple-600', 'bg-purple-600 text-white border-purple-600 shadow-md'],
            IZIN:  ['I', 'bg-[#EFB82D]', 'bg-[#EFB82D] text-white border-[#EFB82D] shadow-md'],
            ABSEN: ['A', 'bg-[#CE1B1B]', 'bg-[#CE1B1B] text-white border-[#CE1B1B] shadow-md']
        };
        const BASE = 'px-2 sm:px-3 py-2 sm:py-1.5 rounded-lg font-bold border text-[11px] transition-all ';
        const NETRAL = {
            MASUK: 'bg-white text-emerald-600 border-emerald-400 hover:bg-emerald-50',
            SAKIT: 'bg-white text-purple-600 border-purple-400 hover:bg-purple-50',
            IZIN:  'bg-white text-amber-600 border-amber-400 hover:bg-amber-50',
            ABSEN: 'bg-white text-rose-600 border-rose-400 hover:bg-rose-50'
        };
        const ID_TOMBOL = { MASUK: 'btn-masuk', SAKIT: 'btn-sakit', IZIN: 'btn-izin', ABSEN: 'btn-absen' };

        // Status '' = belum dipilih
        function selectStatus(siswaId, status) {
            document.getElementById('input-status-' + siswaId).value = status;

            const badge = document.getElementById('badge-' + siswaId);
            Object.keys(ID_TOMBOL).forEach(function (k) {
                const b = document.getElementById(ID_TOMBOL[k] + '-' + siswaId);
                if (b) b.className = BASE + NETRAL[k];
            });

            const base = 'w-8 h-8 md:w-7 md:h-7 flex items-center justify-center font-bold text-xs rounded-lg md:rounded-sm transition-all duration-200 shrink-0 ';
            if (WARNA[status]) {
                badge.textContent = WARNA[status][0];
                badge.className = base + 'text-white shadow-sm ' + WARNA[status][1];
                document.getElementById(ID_TOMBOL[status] + '-' + siswaId).className = BASE + WARNA[status][2];
            } else {
                badge.textContent = '-';
                badge.className = base + 'text-slate-400 bg-slate-100';
            }

            const baris = badge.closest('tr');
            if (baris) baris.classList.remove('bg-rose-50');
            hitungTerisi();
        }

        function semuaInput() { return document.querySelectorAll('input[id^="input-status-"]'); }

        function hitungTerisi() {
            const el = document.getElementById('penghitung');
            if (!el) return;
            let terisi = 0;
            const semua = semuaInput();
            semua.forEach(function (i) { if (i.value) terisi++; });
            el.textContent = 'Terisi ' + terisi + ' / ' + semua.length;
            el.className = 'px-3.5 py-2 rounded-xl font-extrabold ' + (terisi === semua.length && semua.length > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600');
        }

        function pilihSemua() {
            semuaInput().forEach(function (i) { selectStatus(i.id.replace('input-status-', ''), 'MASUK'); });
        }

        function kosongkanSemua() {
            semuaInput().forEach(function (i) { selectStatus(i.id.replace('input-status-', ''), ''); });
        }

        // Semua siswa wajib punya status sebelum disimpan
        function validasiForm(e) {
            let kosong = null, jumlah = 0;
            semuaInput().forEach(function (i) {
                if (!i.value) {
                    jumlah++;
                    const tr = i.closest('tr');
                    if (tr) { tr.classList.add('bg-rose-50'); if (!kosong) kosong = tr; }
                }
            });
            if (jumlah > 0) {
                const pesan = jumlah + ' siswa belum dipilih statusnya. Klik "Pilih Semua (Hadir)" lalu ubah yang tidak hadir.';
                if (typeof uiToast === 'function') {
                    uiToast(pesan, 'error', 'Absensi belum lengkap');
                } else {
                    alert(pesan);
                }
                if (kosong) kosong.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            return true;
        }

        // Sesuaikan tampilan awal dengan status tersimpan (mis. Absensi Utama terkunci / Kelola Absensi)
        document.addEventListener('DOMContentLoaded', function () {
            semuaInput().forEach(function (el) {
                selectStatus(el.id.replace('input-status-', ''), el.value);
            });
        });

        // CARI NAMA SISWA
        function filterSiswaTable() {
            const searchNama = document.getElementById('inputSearchNama').value.toLowerCase();
            const rows = document.querySelectorAll('.row-siswa');

            rows.forEach(row => {
                const nama = row.querySelector('.nama-cell').textContent.toLowerCase();
                row.style.display = nama.includes(searchNama) ? "" : "none";
            });
        }
    </script>
</body>
</html>