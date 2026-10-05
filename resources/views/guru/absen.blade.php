<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Guru - SMP Djojoredjo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EAF0F6] font-sans text-slate-800 antialiased overflow-x-hidden">

    <div class="flex min-h-screen relative">
        @include('layouts.partials.sidebar')

        <main class="flex-1 flex flex-col min-w-0 overflow-y-auto min-h-screen">
            <header class="bg-transparent px-4 sm:px-8 pt-4 pb-2 flex justify-between items-center">
                <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">PRESENSI GURU</h1>
                <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-slate-200/60">
                    <i class="fa-regular fa-calendar text-[#24B9A1] mr-1"></i>
                    <span>{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>

            <div class="px-4 sm:px-8 pb-8 flex items-center justify-center min-h-[calc(100vh-80px)]">
                <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-100 w-full max-w-3xl">
                    <h2 class="text-2xl font-bold text-center text-slate-900 mb-6">Presensi Guru</h2>

                    @if(session('success'))
                        <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-rose-100 border border-rose-300 text-rose-800 text-xs rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- STATUS PRESENSI HARI INI -->
                    <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row justify-around items-center gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-slate-600">Status Hari Ini:</span>
                            @if(optional($absensiHariIni)->status)
                                <span class="px-3 py-1 rounded-full font-bold 
                                    {{ $absensiHariIni->status == 'Hadir' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $absensiHariIni->status == 'Izin' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $absensiHariIni->status == 'Sakit' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ $absensiHariIni->status }} 
                                    @if($absensiHariIni->jam_masuk)
                                        ({{ \Carbon\Carbon::parse($absensiHariIni->jam_masuk)->format('H:i') }} WIB)
                                    @endif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full font-bold bg-slate-200 text-slate-600">Belum Mengisi Presensi</span>
                            @endif
                        </div>
                    </div>

                    <!-- FORM PRESENSI -->
                    <form action="{{ route('guru.absen.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Nama / NUPTK Guru</label>
                                <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full font-semibold p-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-700">
                            </div>
                            <div>
                                <label class="block font-medium text-slate-500 mb-1">Waktu Sekarang (WIB)</label>
                                <input type="text" id="waktu_otomatis" readonly class="w-full font-bold p-3 bg-teal-50 border border-teal-200 rounded-xl text-[#24B9A1]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Pilih Status Kehadiran</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Hadir" class="peer hidden" checked>
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-[#24B9A1] peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Hadir</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Izin" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-amber-500 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Izin</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="status" value="Sakit" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-rose-500 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Sakit</span>
                                </label>
                                <label class="cursor-pointer text-center">
                                    <input type="radio" name="jenis_presensi" value="Pulang" class="peer hidden">
                                    <span class="py-2.5 rounded-xl text-xs font-semibold block peer-checked:bg-blue-600 peer-checked:text-white bg-slate-100 text-slate-600 border border-slate-200 transition">Pulang</span>
                                </label>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">*Pilih "Pulang" jika ingin melakukan absensi jam kepulangan.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Keterangan / Alasan (Opsional untuk Izin/Sakit)</label>
                            <textarea name="keterangan" rows="3" placeholder="Masukkan keterangan jika izin atau sakit..." class="w-full text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-[#24B9A1]"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-[#24B9A1] hover:bg-[#1DA38D] text-white font-semibold rounded-2xl text-xs sm:text-sm shadow-lg transition">
                            Kirim Presensi
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        setInterval(() => {
            const now = new Date();
            document.getElementById('waktu_otomatis').value = now.toLocaleTimeString('id-ID', { timeZone: 'Asia/Jakarta' }) + ' WIB';
        }, 1000);
    </script>
</body>
</html>