<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SppController extends Controller
{
    public function index(Request $request)
    {
        $query = Spp::with(['siswa', 'admin'])->orderBy('tanggal_bayar', 'desc');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        $rekapSpp = $query->paginate(10);
        $siswas = Siswa::orderBy('nama', 'asc')->get();

        $daftarBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('admin.spp.index', compact('rekapSpp', 'siswas', 'daftarBulan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'          => 'required|exists:siswas,id', 
            'bulan'             => 'required|string',
            'tahun'             => 'required|numeric',
            'nominal'           => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string', // <--- DITAMBAHKAN
            'keterangan'        => 'nullable|string'
        ], [
            'siswa_id.required' => 'Silakan ketik dan pilih nama siswa dari daftar yang tersedia.',
            'siswa_id.exists'   => 'Data siswa tidak ditemukan di sistem.'
        ]);

        $sudahBayar = Spp::where('siswa_id', $request->siswa_id)
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun)
            ->exists();

        if ($sudahBayar) {
            return redirect()->back()->with('error', 'Siswa tersebut sudah membayar SPP untuk bulan ' . $request->bulan . ' tahun ' . $request->tahun);
        }

        Spp::create([
            'siswa_id'          => $request->siswa_id,
            'bulan'             => $request->bulan,
            'tahun'             => $request->tahun,
            'nominal'           => $request->nominal,
            'metode_pembayaran' => $request->metode_pembayaran, // <--- DITAMBAHKAN
            'tanggal_bayar'     => Carbon::now(),
            'status'            => 'Lunas',
            'keterangan'        => $request->keterangan,
            'admin_id'          => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pembayaran SPP berhasil dicatat!');
    }
}