<?php

namespace App\Http\Controllers;

use App\Models\IzinGuru;
use App\Services\AbsenSiswaService as Absen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/** Pengajuan izin (Sakit / Dinas) oleh guru, dengan upload bukti. */
class IzinGuruController extends Controller
{
    public function index()
    {
        $izins = IzinGuru::where('user_id', Auth::id())->orderByDesc('created_at')->get();

        return view('guru.izin', compact('izins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis'           => 'required|in:Sakit,Dinas',
            'tanggal_mulai'   => 'required|date_format:Y-m-d',
            'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:500',
            'bukti'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'bukti.required' => 'Bukti wajib diunggah (surat dokter / surat tugas).',
            'bukti.mimes'    => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.max'      => 'Ukuran bukti maksimal 2 MB.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $mulai   = Carbon::createFromFormat('Y-m-d', $data['tanggal_mulai'], Absen::TZ)->startOfDay();
        $selesai = Carbon::createFromFormat('Y-m-d', $data['tanggal_selesai'], Absen::TZ)->startOfDay();

        if ($mulai->lt(Absen::sekarang()->startOfDay()->subDays(7))) {
            return redirect()->back()->withInput()->with('error', 'Pengajuan izin maksimal 7 hari ke belakang.');
        }

        if ($mulai->diffInDays($selesai) > 30) {
            return redirect()->back()->withInput()->with('error', 'Rentang izin maksimal 31 hari. Ajukan terpisah untuk izin yang lebih lama.');
        }

        $bentrok = IzinGuru::where('user_id', Auth::id())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->whereDate('tanggal_mulai', '<=', $selesai->toDateString())
            ->whereDate('tanggal_selesai', '>=', $mulai->toDateString())
            ->exists();

        if ($bentrok) {
            return redirect()->back()->withInput()->with('error', 'Sudah ada pengajuan izin (menunggu/disetujui) yang tumpang tindih dengan tanggal ini.');
        }

        $file = $request->file('bukti');
        $path = $file->store('izin-guru');   // disk privat, tidak dapat diakses langsung lewat URL

        IzinGuru::create([
            'user_id'         => Auth::id(),
            'jenis'           => $data['jenis'],
            'tanggal_mulai'   => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'keterangan'      => $data['keterangan'],
            'bukti_path'      => $path,
            'bukti_nama'      => $file->getClientOriginalName(),
            'status'          => 'menunggu',
        ]);

        return redirect()->route('guru.izin.index')->with('success', 'Pengajuan izin terkirim dan menunggu persetujuan admin.');
    }

    /** Bukti hanya dapat dilihat pemiliknya. */
    public function bukti(IzinGuru $izin)
    {
        abort_unless($izin->user_id === Auth::id(), 403);
        abort_unless(Storage::exists($izin->bukti_path), 404);

        return Storage::response($izin->bukti_path);
    }
}
