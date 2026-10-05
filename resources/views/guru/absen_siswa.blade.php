<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Murid - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans antialiased overflow-x-hidden">

    <div id="mobileOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 p-2 rounded-lg bg-white shadow-sm hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">ABSENSI SISWA</h1>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 flex items-center justify-center min-h-[calc(100vh-80px)]">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 w-full max-w-3xl">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 uppercase border-b border-slate-100 pb-3">PRESENSI MURID</h2>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('guru.absen.simpanSesi') }}" method="POST" class="space-y-5 text-xs">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Mata Pelajaran -->
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Mata Pelajaran</label>
                                <input type="text" name="pembelajaran" value="{{ $defaultMapel ?? 'Bahasa Indonesia' }}" readonly required
                                       class="w-full p-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none cursor-not-allowed">
                            </div>

                            <!-- Pilih Kelas -->
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Kelas</label>
                                <select id="selectKelas" name="kelas" 
                                        data-jadwal="{{ json_encode($jadwalList ?? []) }}"
                                        onchange="updateJamJadwal(this.value)" 
                                        required 
                                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                                    <option value="">-- Pilih Kelas --</option>
                                    @if(isset($kelases) && $kelases->count() > 0)
                                        @foreach($kelases as $kelas)
                                            @php
                                                $cleanUrl = strtolower(trim(str_replace(['kelas', ' '], '', $selectedKelasUrl)));
                                                $cleanKls = strtolower(trim(str_replace(['kelas', ' '], '', $kelas->nama_kelas)));
                                                $isSelected = ($cleanUrl === $cleanKls || str_contains($cleanKls, $cleanUrl) || str_contains($cleanUrl, $cleanKls));
                                            @endphp
                                            <option value="{{ $kelas->nama_kelas }}" {{ $isSelected ? 'selected' : '' }}>
                                                Kelas {{ $kelas->nama_kelas }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ $selectedKelasUrl }}" selected>Kelas {{ $selectedKelasUrl }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <!-- Form Input Pertemuan, Tanggal, Jam, dan Materi -->
                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Pertemuan Ke <span class="text-red-500">*</span></label>
                            <input type="number" name="pertemuan_ke" value="1" min="1" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                                <input type="time" id="jamMulai" name="jam_mulai" value="{{ $defaultJamMasuk ?? '07:00' }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-600 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                                <input type="time" id="jamSelesai" name="jam_selesai" value="{{ $defaultJamSelesai ?? '08:30' }}" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold focus:outline-none focus:border-[#19A389]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-600 mb-1">Materi Pembelajaran / Penjelasan</label>
                            <textarea name="materi" rows="3" placeholder="Masukkan pembahasan materi..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#19A389]"></textarea>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-3.5 bg-[#19A389] hover:bg-[#138A73] text-white font-bold rounded-2xl shadow-lg transition text-center text-xs">
                                Simpan & Lanjut Absen Murid
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateJamJadwal(selectedKelas) {
            if (!selectedKelas) return;

            const selectEl = document.getElementById('selectKelas');
            if (!selectEl) return;

            let jadwalsData = [];
            try {
                const rawData = selectEl.getAttribute('data-jadwal');
                jadwalsData = JSON.parse(rawData || '[]');
            } catch (e) {
                console.error("Gagal membaca data jadwal:", e);
                return;
            }

            const cleanSelected = selectedKelas.toString().toLowerCase().replace(/kelas/g, '').trim();

            const foundJadwal = jadwalsData.find(function(j) {
                if (!j || !j.kelas) return false;
                const cleanJadwal = j.kelas.toString().toLowerCase().replace(/kelas/g, '').trim();
                return cleanJadwal === cleanSelected || cleanJadwal.includes(cleanSelected) || cleanSelected.includes(cleanJadwal);
            });

            if (foundJadwal) {
                if (foundJadwal.jam_masuk) {
                    document.getElementById('jamMulai').value = foundJadwal.jam_masuk.substring(0, 5);
                }
                if (foundJadwal.jam_selesai) {
                    document.getElementById('jamSelesai').value = foundJadwal.jam_selesai.substring(0, 5);
                }
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            if (sidebar) sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectKelas = document.getElementById('selectKelas');
            if (selectKelas && selectKelas.value) {
                updateJamJadwal(selectKelas.value);
            }
        });
    </script>
</body>
</html>