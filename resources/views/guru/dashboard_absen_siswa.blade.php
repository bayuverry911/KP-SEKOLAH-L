<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Siswa - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">DASHBOARD ABSENSI SISWA</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 space-y-4">
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Dashboard Absensi Siswa</h2>

                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 space-y-6">
                    
                    @if(session('error'))
                        <div class="p-3 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($terkunci ?? false)
                        <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-xl flex items-center gap-2 font-semibold">
                            <i class="fa-solid fa-lock text-emerald-600"></i>
                            <span>Absensi Utama diselesaikan oleh: <b>{{ $selesaiOleh }}</b>. Form terkunci dan tidak dapat diubah.</span>
                        </div>
                    @elseif($utama ?? false)
                        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-xl">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Absensi Utama hanya dilakukan <b>satu kali</b> per hari oleh guru jam ke-1; setelah disimpan, form terkunci.
                            Absensi mata pelajaran tetap diisi masing-masing guru mapel.
                        </div>
                    @endif

                    @if(! ($utama ?? false) && isset($utamaInfo))
                        @if($utamaInfo['ada'])
                            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs rounded-xl flex items-start gap-2">
                                <i class="fa-solid fa-school-circle-check text-emerald-600 mt-0.5"></i>
                                <span>Absensi Utama (sekolah) kelas ini sudah diisi oleh <b>{{ $utamaInfo['oleh'] ?? '-' }}</b>.
                                    Kolom <b>UTAMA</b> menunjukkan kehadiran siswa di jam ke-1 untuk dibandingkan dengan kehadiran di jam Anda.</span>
                            </div>
                        @else
                            <div class="p-3 bg-amber-100 border border-amber-300 text-amber-900 text-xs rounded-xl flex items-start gap-2">
                                <i class="fa-solid fa-bell text-amber-600 mt-0.5"></i>
                                <span>Absensi Utama (sekolah) kelas ini <b>belum diisi</b>@if($utamaInfo['penanggung']) oleh guru jam ke-1 (<b>{{ $utamaInfo['penanggung'] }}</b>)@endif.
                                    Mohon ingatkan agar data kehadiran sekolah lengkap.</span>
                            </div>
                        @endif
                    @endif

                    @if($kelola ?? false)
                        <div class="p-3 bg-sky-50 border border-sky-200 text-sky-900 text-xs rounded-xl flex items-start gap-2">
                            <i class="fa-solid fa-pen-to-square text-sky-600 mt-0.5"></i>
                            <span><b>Kelola Absensi.</b> Ubah status siswa yang salah input, lalu simpan.
                                Perubahan hanya memperbarui data yang sudah ada (tidak menambah baris absensi baru).</span>
                        </div>
                    @endif

                    @if($susulan)
                        <div class="p-3 bg-amber-100 border border-amber-300 text-amber-900 text-xs rounded-xl flex items-start gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-amber-600 mt-0.5"></i>
                            <span><b>Absen Susulan.</b> Tanggal terkunci sesuai jadwal yang terlewat dan tidak dapat diubah.
                                Tinjau dan salin dari catatan fisik.</span>
                        </div>
                    @endif

                    <!-- INFORMASI SESI (otomatis dari jadwal, tanggal terkunci) -->
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-xs">
                        <div class="font-semibold text-slate-500 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 flex flex-wrap items-center gap-x-2 gap-y-1">
                            @if($utama ?? false)
                                <span class="text-indigo-600 font-bold">Absensi Utama (Jam Ke-1)</span>
                                <span>•</span>
                                <span class="font-bold text-slate-700">Kelas {{ $kartu->kelas }}</span>
                                <span>•</span>
                                <span>{{ $kartu->jam_masuk }} WIB</span>
                                <span>•</span>
                            @else
                                @if($kelola ?? false)
                                    <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-700 font-bold text-[10px] uppercase">Kelola Absensi</span>
                                    <span>•</span>
                                @endif
                                <span class="text-[#19A389] font-bold">{{ $kartu->mapel }}</span>
                                <span>•</span>
                                <span class="font-bold text-slate-700">Kelas {{ $kartu->kelas }}</span>
                                <span>•</span>
                                <span>Pertemuan ke-{{ $pertemuan }}</span>
                                <span>•</span>
                                <span>{{ $kartu->jam_masuk }} - {{ $kartu->jam_selesai }} WIB</span>
                                <span>•</span>
                            @endif
                            <span title="Tanggal terkunci otomatis sesuai hari ini">
                                <i class="fa-solid fa-lock text-[10px] text-slate-400 mr-0.5"></i>{{ \App\Services\AbsenSiswaService::namaHari($now) }}, {{ $now->format('d/m/Y') }}
                            </span>
                            @if($kartu->is_pengganti)
                                <span class="px-2 py-0.5 rounded-md bg-amber-400 text-amber-950 font-bold text-[10px]">
                                    Guru Pengganti{{ ($kartu->guru_asli ?? null) ? ' (menggantikan ' . $kartu->guru_asli . ')' : '' }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ $susulan ? ($kembali ?? route('guru.rekap-siswa')) : route('guru.dashboard') }}"
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 transition">
                            <i class="fa-solid fa-arrow-left"></i> {{ $susulan ? 'Kembali ke Rekap' : 'Kembali ke Dashboard' }}
                        </a>
                    </div>
                    <!-- SEARCH BAR (CARI NAMA SAJA) -->
                    <div class="flex items-center gap-3 text-xs max-w-md">
                        <span class="font-bold text-slate-600 whitespace-nowrap">Cari Nama Siswa:</span>
                        <input type="text" id="inputSearchNama" onkeyup="filterSiswaTable()" placeholder="Ketik nama siswa..." 
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                    </div>

                    <!-- FORM SIMPAN BATCH PRESENSI -->
                    <form action="{{ $aksi }}" method="POST" id="formAbsen" onsubmit="return validasiForm(event)">
                        @csrf
                        @if($kelola ?? false) @method('PUT') @endif

                        @unless($terkunci ?? false)
                            <div class="flex flex-wrap items-center gap-2 mb-3 text-xs">
                                <button type="button" onclick="pilihSemua()"
                                        class="px-4 py-2 rounded-xl font-bold bg-[#47CB23] hover:bg-[#3bb61c] text-white shadow-sm transition">
                                    <i class="fa-solid fa-check-double mr-1"></i> Pilih Semua (Hadir)
                                </button>
                                <button type="button" onclick="kosongkanSemua()"
                                        class="px-4 py-2 rounded-xl font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                    <i class="fa-solid fa-eraser mr-1"></i> Kosongkan Semua
                                </button>
                                <span id="penghitung" class="ml-auto px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 font-bold">Terisi 0 / 0</span>
                            </div>
                        @endunless
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                            <table class="w-full text-left text-xs min-w-[700px]" id="siswaTable">
                                <thead class="bg-[#19A389] text-white font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="p-4 w-1/3">NAMA SISWA</th>
                                        <th class="p-4 w-1/6">KELAS</th>
                                        <th class="p-4 w-1/5">WAKTU</th>
                                        @unless($utama ?? false)<th class="p-4 text-center" title="Kehadiran di Absensi Utama (jam ke-1)">UTAMA</th>@endunless
                                        <th class="p-4 text-center">STATUS KEHADIRAN</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                    
                                    @forelse($siswas as $index =>$s)
                                        @php
                                            $siswaId =$s->id ?? ($index + 1);$namaSiswa = $s->nama ?? $s->name ?? '-';
                                            $kelasSiswa = $s->kelas ?? $kartu->kelas;
                                            $statusSiswa = ($tersimpan ?? [])[$siswaId] ?? '';
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition row-siswa">
                                            <td class="p-4 font-bold text-slate-800 nama-cell">{{ $namaSiswa }}</td>
                                            <td class="p-4">{{ $kelasSiswa }}</td>
                                            <td class="p-4 text-slate-500">{{ date('d.m.Y H:i') }}</td>
                                            @unless($utama ?? false)
                                                @php
                                                    $hu = ($utamaInfo['status'] ?? [])[$siswaId] ?? null;
                                                    $gu = match($hu) {
                                                        'H' => 'bg-emerald-500 text-white', 'I' => 'bg-sky-500 text-white',
                                                        'S' => 'bg-amber-400 text-slate-900', 'A' => 'bg-rose-500 text-white',
                                                        default => 'bg-slate-100 text-slate-400',
                                                    };
                                                @endphp
                                                <td class="p-4 text-center">
                                                    <span class="inline-flex w-6 h-6 items-center justify-center rounded-md text-[11px] font-bold {{ $gu }}"
                                                          title="Absensi Utama: {{ $hu ?? 'belum diisi' }}">{{ $hu ?? '?' }}</span>
                                                </td>
                                            @endunless
                                            <td class="p-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    
                                                    <!-- HIDDEN INPUT VALUE TO POST -->
                                                    <input type="hidden" name="presensi[{{ $siswaId }}]" id="input-status-{{ $siswaId }}" value="{{ $statusSiswa }}">

                                                    <!-- BADGE INDIKATOR (H / S / I / A) -->
                                                    <div id="badge-{{ $siswaId }}"
                                                         class="w-7 h-7 flex items-center justify-center font-bold text-slate-400 text-xs rounded-sm bg-slate-100 transition-all duration-200">-</div>

                                                    <!-- TOMBOL PILIHAN STATUS -->
                                                    <div class="flex items-center gap-1.5 {{ ($terkunci ?? false) ? 'pointer-events-none opacity-90' : '' }}">
                                                        <!-- HADIR (MASUK) -->
                                                        <button type="button" onclick="selectStatus('{{ $siswaId }}', 'MASUK')" id="btn-masuk-{{ $siswaId }}"
                                                                class="px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-emerald-600 border-emerald-400 hover:bg-emerald-50">
                                                            HADIR
                                                        </button>

                                                        <!-- SAKIT -->
                                                        <button type="button" onclick="selectStatus('{{ $siswaId }}', 'SAKIT')" id="btn-sakit-{{ $siswaId }}"
                                                                class="px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-purple-600 border-purple-400 hover:bg-purple-50">
                                                            SAKIT
                                                        </button>

                                                        <!-- IZIN -->
                                                        <button type="button" onclick="selectStatus('{{ $siswaId }}', 'IZIN')" id="btn-izin-{{ $siswaId }}"
                                                                class="px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-amber-600 border-amber-400 hover:bg-amber-50">
                                                            IZIN
                                                        </button>

                                                        <!-- ABSEN / ALPA -->
                                                        <button type="button" onclick="selectStatus('{{ $siswaId }}', 'ABSEN')" id="btn-absen-{{ $siswaId }}"
                                                                class="px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-rose-600 border-rose-400 hover:bg-rose-50">
                                                            ABSEN
                                                        </button>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-slate-400 italic">
                                                Belum ada data siswa terdaftar di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>

                        <!-- TOMBOL SELESAI ABSEN -->
                        <div class="pt-6 flex justify-end {{ ($terkunci ?? false) ? 'hidden' : '' }}">
                            <button type="submit" 
                                    class="px-8 py-3 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-2xl shadow-lg transition flex items-center gap-2 text-xs">
                                <i class="fa-solid fa-circle-check"></i> {{ ($kelola ?? false) ? 'Simpan Perubahan' : 'Selesai Absen' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        const WARNA = {
            MASUK: ['H', 'bg-[#47CB23]', 'bg-[#47CB23] text-white border-[#47CB23] shadow-md'],
            SAKIT: ['S', 'bg-purple-600', 'bg-purple-600 text-white border-purple-600 shadow-md'],
            IZIN:  ['I', 'bg-[#EFB82D]', 'bg-[#EFB82D] text-white border-[#EFB82D] shadow-md'],
            ABSEN: ['A', 'bg-[#CE1B1B]', 'bg-[#CE1B1B] text-white border-[#CE1B1B] shadow-md']
        };
        const BASE = 'px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all ';
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

            const base = 'w-7 h-7 flex items-center justify-center font-bold text-xs rounded-sm transition-all duration-200 ';
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
            el.className = 'ml-auto px-3 py-1.5 rounded-full font-bold ' + (terisi === semua.length && semua.length > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600');
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
                uiToast(jumlah + ' siswa belum dipilih statusnya. Klik "Pilih Semua (Hadir)" lalu ubah yang tidak hadir.', 'error', 'Absensi belum lengkap');
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
                if (nama.includes(searchNama)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>