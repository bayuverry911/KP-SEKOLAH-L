<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::latest()->get();
        return view('admin.data-guru.index', compact('gurus'));
    }

    public function create()
    {
        $users = User::whereRaw('LOWER(role::text) = ?', ['guru'])->get();
        
        $kelases = class_exists('App\Models\Kelas') ? \App\Models\Kelas::all() : collect();
        $mapels  = class_exists('App\Models\Mapel') ? \App\Models\Mapel::all() : collect();

        return view('admin.data-guru.create', compact('users', 'kelases', 'mapels'));
    }

    public function store(Request $request)
    {
        if ($request->has('kelas') && is_array($request->kelas)) {
            $request->merge([
                'kelas' => implode(', ', $request->kelas)
            ]);
        }

        // Validasi user_id diperbarui mengarah ke tabel 'profiles'
        $validated = $request->validate([
            'user_id'        => 'nullable|exists:profiles,id',
            'nama'           => 'required|string|max:255',
            'nuptk'          => 'nullable|string|max:50|unique:gurus,nuptk',
            'jenis_kelamin'  => 'required|in:L,P',
            'mata_pelajaran' => 'required|string|max:100',
            'no_telepon'     => 'nullable|string|max:20',
            'kelas'          => 'nullable|string|max:255',
        ]);

        Guru::create($validated);

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
    }

    // PERBAIKAN: Ubah $guru menjadi $data_guru agar Route Model Binding Laravel berfungsi
    public function edit(Guru $data_guru)
    {
        $guru = $data_guru; // Kita masukkan kembali ke $guru agar view tidak perlu diubah

        $users = User::whereRaw('LOWER(role::text) = ?', ['guru'])->get();

        $kelases = class_exists('App\Models\Kelas') ? \App\Models\Kelas::all() : collect();
        $mapels  = class_exists('App\Models\Mapel') ? \App\Models\Mapel::all() : collect();

        return view('admin.data-guru.edit', compact('guru', 'users', 'kelases', 'mapels'));
    }

    // PERBAIKAN: Ubah $guru menjadi $data_guru
    public function update(Request $request, Guru $data_guru)
    {
        $guru = $data_guru;

        if ($request->has('kelas') && is_array($request->kelas)) {
            $request->merge([
                'kelas' => implode(', ', $request->kelas)
            ]);
        }

        // Validasi user_id diperbarui mengarah ke tabel 'profiles'
        $validated = $request->validate([
            'user_id'        => 'nullable|exists:profiles,id',
            'nama'           => 'required|string|max:255',
            'nuptk'          => 'nullable|string|max:50|unique:gurus,nuptk,' . $guru->id,
            'jenis_kelamin'  => 'required|in:L,P',
            'mata_pelajaran' => 'required|string|max:100',
            'no_telepon'     => 'nullable|string|max:20',
            'kelas'          => 'nullable|string|max:255',
        ]);

        $guru->update($validated);

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui!');
    }

    // PERBAIKAN: Ubah $guru menjadi $data_guru
    public function destroy(Guru $data_guru)
    {
        $data_guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus!');
    }
}