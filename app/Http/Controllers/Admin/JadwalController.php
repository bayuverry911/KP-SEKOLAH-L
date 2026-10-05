<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwals = Jadwal::latest()->paginate(15);
        $gurus = class_exists(Guru::class) ? Guru::all() : DB::table('gurus')->get();
        $kelases = class_exists(Kelas::class) ? Kelas::all() : DB::table('kelases')->get();
        $mapels = class_exists(Mapel::class) ? Mapel::all() : DB::table('mapels')->get();

        return view('admin.jadwal.index', compact('jadwals', 'gurus', 'kelases', 'mapels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id'     => 'required',
            'kelas'       => 'required',
            'mapel'       => 'required',
            'hari'        => 'required',
            'jam_masuk'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $guru = Guru::find($request->guru_id) ?? DB::table('gurus')->where('id', $request->guru_id)->first();
        $namaGuru = $guru->nama ?? $guru->nama_guru ?? '-';

        Jadwal::create([
            'guru_id'     => $request->guru_id,
            'nama_guru'   => $namaGuru,
            'kelas'       => $request->kelas,
            'mapel'       => $request->mapel,
            'hari'        => $request->hari,
            'jam_masuk'   => $request->jam_masuk,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->back()->with('success', 'Jadwal mengajar berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        Jadwal::destroy($id);
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }
}