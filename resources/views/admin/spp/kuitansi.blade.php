<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi {{ $spp->no_transaksi }} - {{ config('sekolah.nama') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .kertas { box-shadow: none !important; border: 1px solid #000 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased p-4 sm:p-8">

    <div class="no-print max-w-3xl mx-auto mb-4 flex justify-between items-center">
        <a href="{{ route('admin.spp.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-[#24B9A1] hover:bg-[#1DA38D] text-white text-xs font-semibold rounded-xl shadow">
            Cetak Kuitansi
        </button>
    </div>

    <div class="kertas max-w-3xl mx-auto bg-white p-8 sm:p-10 rounded-2xl shadow-xl border border-slate-200">
        <div class="flex items-center gap-4 pb-4 border-b-2 border-slate-800">
            <img src="{{ asset('images/logo_smp_djojoredjo.jpeg') }}" alt="Logo" class="w-16 h-16 object-contain">
            <div class="flex-1">
                <h1 class="text-xl font-extrabold uppercase tracking-wide">{{ config('sekolah.nama') }}</h1>
                @if(config('sekolah.alamat'))
                    <p class="text-xs text-slate-500">{{ config('sekolah.alamat') }}</p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-[10px] uppercase text-slate-500 font-semibold">No. Kuitansi</p>
                <p class="font-mono font-bold text-sm">{{ $spp->no_transaksi }}</p>
            </div>
        </div>

        <h2 class="text-center text-lg font-bold tracking-widest uppercase my-5">Kuitansi Pembayaran SPP</h2>

        <table class="w-full text-sm">
            <tr>
                <td class="py-1.5 w-44 text-slate-500">Telah terima dari</td>
                <td class="py-1.5 font-semibold">: Wali/Siswa an. {{ $spp->siswa->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 text-slate-500">NISN / Kelas</td>
                <td class="py-1.5">: {{ $spp->siswa->nisn ?? '-' }} / {{ $spp->siswa->kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="py-1.5 text-slate-500">Untuk pembayaran</td>
                <td class="py-1.5">: SPP bulan <span class="font-semibold">{{ $spp->bulan }} {{ $spp->tahun }}</span></td>
            </tr>
            <tr>
                <td class="py-1.5 text-slate-500">Metode pembayaran</td>
                <td class="py-1.5">: {{ $spp->metode_pembayaran }}</td>
            </tr>
            <tr>
                <td class="py-1.5 text-slate-500">Terbilang</td>
                <td class="py-1.5 italic">: {{ \App\Support\Terbilang::rupiah($spp->nominal) }}</td>
            </tr>
            @if($spp->keterangan)
            <tr>
                <td class="py-1.5 text-slate-500">Keterangan</td>
                <td class="py-1.5">: {{ $spp->keterangan }}</td>
            </tr>
            @endif
        </table>

        <div class="flex flex-col sm:flex-row justify-between items-end gap-6 mt-8">
            <div class="border-2 border-slate-800 px-5 py-3 rounded-lg">
                <p class="text-[10px] uppercase text-slate-500 font-semibold">Jumlah</p>
                <p class="text-2xl font-extrabold">Rp {{ number_format($spp->nominal, 0, ',', '.') }}</p>
                <p class="text-xs font-bold text-emerald-700 mt-1 uppercase">{{ $spp->status }}</p>
            </div>

            <div class="text-center text-sm">
                <p>{{ \Carbon\Carbon::parse($spp->tanggal_bayar)->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</p>
                <p class="text-slate-500 text-xs">Petugas / Bendahara</p>
                <div class="h-16"></div>
                <p class="font-semibold underline">{{ $spp->admin->name ?? '-' }}</p>
            </div>
        </div>

        <p class="text-[10px] text-slate-400 mt-8 text-center">Simpan kuitansi ini sebagai bukti pembayaran yang sah. Nomor kuitansi tercatat pada sistem.</p>
    </div>
</body>
</html>
