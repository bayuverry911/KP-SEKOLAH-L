<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh akun pengguna
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.kelola-akun', compact('users'));
    }

    /**
     * Menampilkan form tambah akun pengguna
     */
    public function create()
    {
        return view('admin.tambah-akun');
    }

    /**
     * Menyimpan data akun pengguna baru (disimpan ke tabel profiles)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,ADMIN,GURU'],
        ]);

        $role = strtoupper($request->role);

        // Simpan akun langsung ke tabel profiles di PostgreSQL lokal
        DB::table('profiles')->insert([
            'id'         => (string) Str::uuid(),
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $role,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil terdaftar di sistem!');
    }

    /**
     * Menampilkan detail informasi akun pengguna
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Menampilkan form edit akun pengguna
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data akun pengguna yang ada
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('profiles', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,guru,ADMIN,GURU'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => strtoupper($request->role),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::transaction(function () use ($request, $user, $data) {
            $user->update($data);
        });

        return redirect()->route('admin.users.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Menghapus akun pengguna dari database
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            // Hapus dari profiles
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }
}