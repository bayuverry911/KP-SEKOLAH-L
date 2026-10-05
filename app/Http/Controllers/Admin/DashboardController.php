<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\AbsensiGuru;
use App\Models\Spp;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Sekolah (Hitung total dari database)
        $totalGuru = Guru::count();
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::count();

        // 2. Absensi Guru Hari Ini
        $today = Carbon::today()->toDateString();
        $absenHariIni = AbsensiGuru::whereDate('tanggal', $today)->get();
        
        $hadirGuru = $absenHariIni->where('status', 'Hadir')->count();
        $izinGuru  = $absenHariIni->where('status', 'Izin')->count();
        $sakitGuru = $absenHariIni->where('status', 'Sakit')->count();
        $alpaGuru  = $absenHariIni->where('status', 'Alpa')->count();

        // Ambil rekap presensi guru terbaru untuk tabel
        $absensiGuruTerbaru = AbsensiGuru::latest('tanggal')->latest('created_at')->take(5)->get();

        // 3. Statistik SPP Bulan Ini
        $bulanIni = Carbon::now()->translatedFormat('F'); // Contoh: September / Oktober
        $tahunIni = Carbon::now()->year;

        $sppBulanIni = Spp::where('bulan', $bulanIni)->where('tahun', $tahunIni)->get();
        
        $lunasSpp = $sppBulanIni->where('status', 'Lunas')->count();
        // Total siswa dikurangi yang sudah bayar (sebagai estimasi belum bayar)
        $belumBayarSpp = max(0, $totalSiswa - $lunasSpp); 
        $totalPemasukanSpp = $sppBulanIni->sum('nominal');

        // Ambil pembayaran SPP terbaru untuk tabel
        $sppTerbaru = Spp::with('siswa')->latest('tanggal_bayar')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalGuru', 'totalSiswa', 'totalKelas', 'totalMapel',
            'hadirGuru', 'izinGuru', 'sakitGuru', 'alpaGuru',
            'absensiGuruTerbaru',
            'lunasSpp', 'belumBayarSpp', 'totalPemasukanSpp', 'sppTerbaru'
        ));
    }
}