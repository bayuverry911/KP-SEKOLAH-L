<?php

namespace App\Http\Controllers;

use App\Models\IzinGuru;
use App\Services\AbsenSiswaService as Absen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/** Pengajuan izin (Sakit / Dinas) oleh guru, dengan upload bukti. */
class IzinGuruController extends Controller
{
    public function index()
    {
        $izins = IzinGuru::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('guru.izin', compact('izins'));
    }

    public function store(Request $request)
    {
        // Normalisasi jika form lama mengirim field 'tanggal' tunggal
        if ($request->filled('tanggal') && ! $request->filled('tanggal_mulai')) {
            $request->merge([
                'tanggal_mulai'   => $request->input('tanggal'),
                'tanggal_selesai' => $request->input('tanggal_selesai', $request->input('tanggal')),
            ]);
        }

        $data = $request->validate([
            'jenis'           => 'required|in:Sakit,Dinas',
            'tanggal_mulai'   => 'required|date_format:Y-m-d',
            'tanggal_selesai' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:500',
            'bukti'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'jenis.required'                 => 'Jenis izin wajib dipilih.',
            'tanggal_mulai.required'         => 'Tanggal mulai izin wajib diisi.',
            'tanggal_selesai.required'       => 'Tanggal selesai izin wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'keterangan.required'            => 'Keterangan izin wajib diisi.',
            'bukti.required'                 => 'Bukti wajib diunggah (surat dokter / surat tugas).',
            'bukti.mimes'                    => 'Bukti harus berupa JPG, PNG, atau PDF.',
            'bukti.max'                      => 'Ukuran bukti maksimal 2 MB.',
        ]);

        $tz      = defined(Absen::class . '::TZ') ? Absen::TZ : 'Asia/Jakarta';
        $mulai   = Carbon::createFromFormat('Y-m-d', $data['tanggal_mulai'], $tz)->startOfDay();
        $selesai = Carbon::createFromFormat('Y-m-d', $data['tanggal_selesai'], $tz)->startOfDay();
        $hariIni = method_exists(Absen::class, 'sekarang')
            ? Absen::sekarang()->startOfDay()
            : Carbon::now($tz)->startOfDay();

        if ($mulai->lt($hariIni->copy()->subDays(7))) {
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
        $path = $file->store('izin-guru'); // disk privat, tidak dapat diakses langsung lewat URL

        $payload = [
            'user_id'         => Auth::id(),
            'jenis'           => $data['jenis'],
            'tanggal_mulai'   => $mulai->toDateString(),
            'tanggal_selesai' => $selesai->toDateString(),
            'keterangan'      => $data['keterangan'],
            'bukti_path'      => $path,
            'bukti_nama'      => $file->getClientOriginalName(),
            'status'          => 'menunggu',
        ];

        // Jaga kompatibilitas jika tabel masih memiliki kolom 'tanggal' not-null
        if (Schema::hasColumn('izin_gurus', 'tanggal')) {
            $payload['tanggal'] = $mulai->toDateString();
        }

        IzinGuru::create($payload);

        return redirect()->route('guru.izin.index')->with('success', 'Pengajuan izin terkirim dan menunggu persetujuan admin.');
    }

    /** Bukti hanya dapat dilihat pemiliknya. */
    public function bukti(IzinGuru $izin)
    {
        abort_unless($izin->user_id === Auth::id(), 403);
        abort_unless($izin->bukti_path && Storage::exists($izin->bukti_path), 404);

        return Storage::response($izin->bukti_path);
    }
}