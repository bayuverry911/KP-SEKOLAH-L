<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AbsenSiswaService as Absen;
use Illuminate\Http\Request;

/** Rekap Absensi Utama (sekolah) untuk admin: seluruh kelas. */
class RekapUtamaController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Absen::semuaKelas();

        return view('shared.rekap_utama', Absen::dataRekapUtama($request, $kelasList, route('admin.rekap-utama')));
    }
}
