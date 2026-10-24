<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\JadwalPengganti;
use App\Services\AbsenSiswaService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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

        // Guru pengganti yang sudah di-plot (hari ini dan ke depan)
        $penggantis = JadwalPengganti::with('jadwal')
            ->where('tanggal', '>=', AbsenSiswaService::sekarang()->toDateString())
            ->orderBy('tanggal')
            ->get();

        return view('admin.jadwal.index', compact('jadwals', 'gurus', 'kelases', 'mapels', 'penggantis'));
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

    /**
     * Plot guru pengganti untuk SATU tanggal tertentu.
     * Jadwal otomatis muncul di dashboard guru pengganti pada tanggal tsb.
     */
    public function storePengganti(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'tanggal'    => 'required|date_format:Y-m-d',
            'guru_id'    => 'required|exists:gurus,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $tanggal = Carbon::createFromFormat('Y-m-d', $request->tanggal, AbsenSiswaService::TZ)->startOfDay();

        if ($tanggal->lt(AbsenSiswaService::sekarang()->startOfDay())) {
            return redirect()->back()->with('error', 'Tanggal guru pengganti tidak boleh di masa lalu.');
        }

        $hariTanggal = AbsenSiswaService::namaHari($tanggal);

        if (mb_strtolower((string) $jadwal->hari) !== mb_strtolower($hariTanggal)) {
            return redirect()->back()->with(
                'error',
                "Tanggal {$tanggal->format('d/m/Y')} jatuh pada hari {$hariTanggal}, sedangkan jadwal ini hari {$jadwal->hari}. Pilih tanggal yang sesuai."
            );
        }

        if ((string) $request->guru_id === (string) $jadwal->guru_id) {
            return redirect()->back()->with('error', 'Guru pengganti harus berbeda dari guru yang dijadwalkan.');
        }

        if (JadwalPengganti::where('jadwal_id', $jadwal->id)->whereDate('tanggal', $tanggal->toDateString())->exists()) {
            return redirect()->back()->with('error', 'Jadwal ini sudah memiliki guru pengganti pada tanggal tersebut. Hapus dulu bila ingin mengganti.');
        }

        $guru = Guru::findOrFail($request->guru_id);

        JadwalPengganti::create([
            'jadwal_id'   => $jadwal->id,
            'tanggal'     => $tanggal->toDateString(),
            'guru_id'     => (string) $guru->id,
            'nama_guru'   => $guru->nama,
            'keterangan'  => $request->keterangan,
            'dibuat_oleh' => Auth::id(),
        ]);

        return redirect()->back()->with(
            'success',
            "{$guru->nama} ditugaskan menggantikan {$jadwal->nama_guru} ({$jadwal->kelas} - {$jadwal->mapel}) pada {$tanggal->format('d/m/Y')}."
        );
    }

    public function destroyPengganti(JadwalPengganti $pengganti)
    {
        $pengganti->delete();

        return redirect()->back()->with('success', 'Penugasan guru pengganti dibatalkan.');
    }
}
