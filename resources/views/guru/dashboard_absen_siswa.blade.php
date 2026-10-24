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

                    <!-- INFORMASI SESI (otomatis dari jadwal hari ini, tidak bisa diubah) -->
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 text-xs">
                        <div class="font-semibold text-slate-500 bg-slate-50 p-3 rounded-2xl border border-slate-200/80 flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="text-[#19A389] font-bold">{{ $kartu->mapel }}</span>
                            <span>•</span>
                            <span class="font-bold text-slate-700">Kelas {{ $kartu->kelas }}</span>
                            <span>•</span>
                            <span>Pertemuan ke-{{ $pertemuan }}</span>
                            <span>•</span>
                            <span>{{ $kartu->jam_masuk }} - {{ $kartu->jam_selesai }} WIB</span>
                            <span>•</span>
                            <span title="Tanggal terkunci otomatis sesuai hari ini">
                                <i class="fa-solid fa-lock text-[10px] text-slate-400 mr-0.5"></i>{{ \App\Services\AbsenSiswaService::namaHari($now) }}, {{ $now->format('d/m/Y') }}
                            </span>
                            @if($kartu->is_pengganti)
                                <span class="px-2 py-0.5 rounded-md bg-amber-400 text-amber-950 font-bold text-[10px]">
                                    Guru Pengganti{{ $kartu->guru_asli ? ' (menggantikan ' . $kartu->guru_asli . ')' : '' }}
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('guru.dashboard') }}"
                           class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 transition">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                    <!-- SEARCH BAR (CARI NAMA SAJA) -->
                    <div class="flex items-center gap-3 text-xs max-w-md">
                        <span class="font-bold text-slate-600 whitespace-nowrap">Cari Nama Siswa:</span>
                        <input type="text" id="inputSearchNama" onkeyup="filterSiswaTable()" placeholder="Ketik nama siswa..." 
                               class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-[#19A389]">
                    </div>

                    <!-- FORM SIMPAN BATCH PRESENSI -->
                    <form action="{{ route('guru.absen-siswa.simpan', $jadwal->id) }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                            <table class="w-full text-left text-xs min-w-[700px]" id="siswaTable">
                                <thead class="bg-[#19A389] text-white font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="p-4 w-1/3">NAMA SISWA</th>
                                        <th class="p-4 w-1/6">KELAS</th>
                                        <th class="p-4 w-1/5">WAKTU</th>
                                        <th class="p-4 text-center">STATUS KEHADIRAN</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                    
                                    @forelse($siswas as $index =>$s)
                                        @php
                                            $siswaId =$s->id ?? ($index + 1);$namaSiswa = $s->nama ?? $s->name ?? '-';
                                            $kelasSiswa = $s->kelas ?? $kartu->kelas;
                                            $statusSiswa = 'MASUK';
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition row-siswa">
                                            <td class="p-4 font-bold text-slate-800 nama-cell">{{ $namaSiswa }}</td>
                                            <td class="p-4">{{ $kelasSiswa }}</td>
                                            <td class="p-4 text-slate-500">{{ date('d.m.Y H:i') }}</td>
                                            <td class="p-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    
                                                    <!-- HIDDEN INPUT VALUE TO POST -->
                                                    <input type="hidden" name="presensi[{{ $siswaId }}]" id="input-status-{{ $siswaId }}" value="{{ $statusSiswa }}">

                                                    <!-- BADGE INDIKATOR (H / S / I / A) -->
                                                    <div id="badge-{{ $siswaId }}" 
                                                         class="w-7 h-7 flex items-center justify-center font-bold text-white text-xs rounded-sm shadow-sm transition-all duration-200 bg-[#47CB23]">
                                                        H
                                                    </div>

                                                    <!-- TOMBOL PILIHAN STATUS -->
                                                    <div class="flex items-center gap-1.5">
                                                        <!-- HADIR (MASUK) -->
                                                        <button type="button" onclick="selectStatus('{{ $siswaId }}', 'MASUK')" id="btn-masuk-{{ $siswaId }}"
                                                                class="px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-[#47CB23] text-white border-[#47CB23] shadow-sm">
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
                                            <td colspan="4" class="p-8 text-center text-slate-400 italic">
                                                Belum ada data siswa terdaftar di kelas ini.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>

                        <!-- TOMBOL SELESAI ABSEN -->
                        <div class="pt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-8 py-3 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-2xl shadow-lg transition flex items-center gap-2 text-xs">
                                <i class="fa-solid fa-circle-check"></i> Selesai Absen
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

        // LOGIC UPDATE TAMPILAN STATUS (HADIR, SAKIT, IZIN, ABSEN)
        function selectStatus(siswaId, status) {
            document.getElementById(`input-status-${siswaId}`).value = status;

            const badge = document.getElementById(`badge-${siswaId}`);
            const btnMasuk = document.getElementById(`btn-masuk-${siswaId}`);
            const btnSakit = document.getElementById(`btn-sakit-${siswaId}`);
            const btnIzin = document.getElementById(`btn-izin-${siswaId}`);
            const btnAbsen = document.getElementById(`btn-absen-${siswaId}`);

            // Reset Kelas Warna Tombol
            btnMasuk.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-blue-500 border-blue-400 hover:bg-blue-50";
            btnSakit.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-purple-600 border-purple-400 hover:bg-purple-50";
            btnIzin.className  = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-amber-600 border-amber-400 hover:bg-amber-50";
            btnAbsen.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-white text-rose-600 border-rose-400 hover:bg-rose-50";

            if (status === 'MASUK') {
                badge.innerText = 'H';
                badge.className = "w-7 h-7 flex items-center justify-center font-bold text-white text-xs rounded-sm shadow-sm transition-all bg-[#47CB23]";
                btnMasuk.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-[#47CB23] text-white border-[#47CB23] shadow-md";
            } else if (status === 'SAKIT') {
                badge.innerText = 'S';
                badge.className = "w-7 h-7 flex items-center justify-center font-bold text-white text-xs rounded-sm shadow-sm transition-all bg-purple-600";
                btnSakit.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-purple-600 text-white border-purple-600 shadow-md";
            } else if (status === 'IZIN') {
                badge.innerText = 'I';
                badge.className = "w-7 h-7 flex items-center justify-center font-bold text-white text-xs rounded-sm shadow-sm transition-all bg-[#EFB82D]";
                btnIzin.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-[#EFB82D] text-white border-[#EFB82D] shadow-md";
            } else if (status === 'ABSEN') {
                badge.innerText = 'A';
                badge.className = "w-7 h-7 flex items-center justify-center font-bold text-white text-xs rounded-sm shadow-sm transition-all bg-[#CE1B1B]";
                btnAbsen.className = "px-3 py-1.5 rounded-lg font-bold border text-[11px] transition-all bg-[#CE1B1B] text-white border-[#CE1B1B] shadow-md";
            }
        }

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