<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\HariLibur;
use App\Models\IzinGuru;
use App\Services\AbsenSiswaService as Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** Persetujuan izin guru oleh admin. Izin disetujui -> presensi guru terisi & terkunci. */
class IzinGuruApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), ['menunggu', 'disetujui', 'ditolak', 'semua'], true)
            ? $request->query('status') : 'menunggu';

        $q = IzinGuru::with('user')->orderByDesc('created_at');

        if ($status !== 'semua') {
            $q->where('status', $status);
        }

        $izins   = $q->paginate(15)->withQueryString();
        $hitung  = IzinGuru::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');

        return view('admin.izin.index', compact('izins', 'status', 'hitung'));
    }

    public function setujui(IzinGuru $izin)
    {
        if ($izin->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $user = $izin->user;
        $guru = $user ? Absen::guruDari($user) : null;

        $dibuat = 0;
        $dilewati = 0;

        DB::transaction(function () use ($izin, $user, $guru, &$dibuat, &$dilewati) {
            $izin->update([
                'status'        => 'disetujui',
                'diproses_oleh' => Auth::id(),
                'diproses_pada' => now(),
            ]);

            $statusAbsen = $izin->jenis === 'Sakit' ? 'Sakit' : 'Izin';
            $nuptk       = optional($guru)->nuptk ?? optional($guru)->nip ?? optional($user)->username ?? '-';
            $label       = $izin->jenis === 'Dinas' ? 'Dinas' : 'Sakit';

            for ($d = $izin->tanggal_mulai->copy(); $d->lte($izin->tanggal_selesai); $d->addDay()) {
                // Minggu & hari libur tidak perlu presensi
                if ($d->dayOfWeekIso === 7 || HariLibur::whereDate('tanggal', $d->toDateString())->exists()) {
                    continue;
                }

                $absen = AbsensiGuru::firstOrNew(['user_id' => $izin->user_id, 'tanggal' => $d->toDateString()]);

                // Guru sudah terlanjur presensi hadir pada hari itu: jangan ditimpa
                if ($absen->exists && $absen->jam_masuk) {
                    $dilewati++;
                    continue;
                }

                $absen->nama_guru  = optional($user)->name ?? '-';
                $absen->nuptk_nip  = $nuptk;
                $absen->status     = $statusAbsen;
                $absen->jam_masuk  = null;
                $absen->jam_keluar = null;
                $absen->keterangan = "Izin {$label} disetujui: " . $izin->keterangan;
                $absen->izin_id    = $izin->id;
                $absen->terkunci   = true;
                $absen->save();
                $dibuat++;
            }
        });

        $pesan = "Izin disetujui. {$dibuat} hari presensi guru diperbarui dan dikunci.";
        if ($dilewati > 0) {
            $pesan .= " {$dilewati} hari dilewati karena guru sudah presensi hadir.";
        }
        $pesan .= ' Jangan lupa plot guru pengganti di menu Jadwal Mengajar bila guru mengajar pada tanggal tersebut.';

        return redirect()->back()->with('success', $pesan);
    }

    public function tolak(Request $request, IzinGuru $izin)
    {
        $data = $request->validate(['catatan_admin' => 'nullable|string|max:500']);

        if ($izin->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $izin->update([
            'status'        => 'ditolak',
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'diproses_oleh' => Auth::id(),
            'diproses_pada' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin ditolak.');
    }

    public function bukti(IzinGuru $izin)
    {
        abort_unless(Storage::exists($izin->bukti_path), 404);

        return Storage::response($izin->bukti_path);
    }
}
