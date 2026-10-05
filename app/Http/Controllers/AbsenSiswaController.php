<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbsenSiswaController extends Controller
{
    // Menampilkan halaman Absen Siswa (Guru)
    public function index(Request $request)
    {
        // Contoh Data Dummy atau Panggil dari Model Siswa
        // $siswas = Siswa::when($request->search_nama, fn($q) => $q->where('nama', 'like', "%{$request->search_nama}%"))
        //                ->when($request->search_kelas, fn($q) => $q->where('kelas', $request->search_kelas))
        //                ->get();

        return view('guru.absen_siswa');
    }

    // Endpoint AJAX untuk update status absensi tanpa reload halaman
    public function updateStatus(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'status'   => 'required|in:H,A,I',
        ]);

        // SIMPAN KE DATABASE (Sesuaikan dengan nama Model Absensi Anda)
        /*
        \App\Models\AbsensiSiswa::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'tanggal'  => now()->toDateString(),
            ],
            [
                'status'   => $request->status,
            ]
        );
        */

        // Kirim respons JSON ke AJAX
        return response()->json([
            'success' => true,
            'message' => 'Status presensi berhasil diperbarui!',
            'status'  => $request->status,
            'siswa_id'=> $request->siswa_id
        ]);
    }

    // Menampilkan Halaman Absensi Siswa (Role SISWA)
    public function siswaIndex()
    {
        return view('siswa.dashboard');
    }
}