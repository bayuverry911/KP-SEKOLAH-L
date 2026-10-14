<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Spp;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $search = $request->search;

            // Cari berdasarkan nama / NISN siswa ATAU nomor transaksi
            $query->where(function ($utama) use ($search) {
                $utama->whereHas('siswa', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%')
                      ->orWhere('nisn', 'like', '%' . $search . '%');
                })->orWhere('no_transaksi', 'like', '%' . $search . '%');
            });
        }

        $rekapSpp = $query->paginate(10)->appends($request->query());
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
            'metode_pembayaran' => 'required|string',
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

        // Transaksi: data SPP + catatan audit tersimpan bersama, atau tidak sama sekali
        $spp = DB::transaction(function () use ($request) {
            return Spp::create([
                'no_transaksi'      => Spp::generateNomor(),
                'siswa_id'          => $request->siswa_id,
                'bulan'             => $request->bulan,
                'tahun'             => $request->tahun,
                'nominal'           => $request->nominal,
                'metode_pembayaran' => $request->metode_pembayaran,
                'tanggal_bayar'     => Carbon::now(),
                'status'            => 'Lunas',
                'keterangan'        => $request->keterangan,
                'admin_id'          => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Pembayaran SPP berhasil dicatat! No. Kuitansi: ' . $spp->no_transaksi);
    }

    /**
     * Koreksi data SPP. Wajib menyertakan alasan; tercatat di audit trail.
     * Nomor transaksi, siswa, dan periode tidak dapat diubah.
     */
    public function update(Request $request, Spp $spp)
    {
        $request->validate([
            'nominal'           => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:Tunai,Transfer Bank,QRIS',
            'keterangan'        => 'nullable|string|max:500',
            'alasan'            => 'required|string|min:5|max:500',
        ], [
            'alasan.required' => 'Alasan perubahan wajib diisi.',
            'alasan.min'      => 'Alasan perubahan minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($request, $spp) {
            $spp->update($request->only('nominal', 'metode_pembayaran', 'keterangan'));
        });

        if (! $spp->wasChanged()) {
            return redirect()->back()->with('error', 'Tidak ada data yang berubah.');
        }

        return redirect()->back()->with('success', 'Data SPP ' . $spp->no_transaksi . ' berhasil diperbarui.');
    }

    /**
     * Hapus data SPP. Wajib menyertakan alasan; data lengkap tersimpan di audit trail.
     */
    public function destroy(Request $request, Spp $spp)
    {
        $request->validate([
            'alasan' => 'required|string|min:5|max:500',
        ], [
            'alasan.required' => 'Alasan penghapusan wajib diisi.',
            'alasan.min'      => 'Alasan penghapusan minimal 5 karakter.',
        ]);

        $nomor = $spp->no_transaksi;

        DB::transaction(function () use ($spp) {
            $spp->delete();
        });

        return redirect()->back()->with('success', 'Data SPP ' . $nomor . ' berhasil dihapus dan tercatat di audit trail.');
    }

    /**
     * Halaman kuitansi yang siap dicetak.
     */
    public function kuitansi(Spp $spp)
    {
        $spp->load(['siswa', 'admin']);

        return view('admin.spp.kuitansi', compact('spp'));
    }

    /**
     * Riwayat audit trail SPP.
     */
    public function audit(Request $request)
    {
        $query = AuditLog::where('modul', 'spp')->orderByDesc('created_at')->orderByDesc('id');

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', '%' . $search . '%')
                  ->orWhere('user_name', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->paginate(15)->appends($request->query());

        return view('admin.spp.audit', compact('logs'));
    }
}
