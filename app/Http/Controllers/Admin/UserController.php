<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
     * Menyimpan data akun pengguna baru (DAFTAR VIA SUPABASE API & SYNC PROFILES)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:profiles,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,ADMIN,GURU'],
        ]);

        $baseUrl = rtrim(env('SUPABASE_URL'), '/');
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?? env('SUPABASE_ANON_KEY');
        $role = strtoupper($request->role);

        // 1. DAFTARKAN USER SECARA RESMI KE SUPABASE AUTH
        $response = Http::withHeaders([
            'apikey'        => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
            'Content-Type'  => 'application/json',
        ])->post("{$baseUrl}/auth/v1/admin/users", [
            'email'         => $request->email,
            'password'      => $request->password,
            'email_confirm' => true,
            'user_metadata' => [
                'name' => $request->name,
                'role' => $role,
            ],
        ]);

        $userId = null;

        if ($response->successful()) {
            $data = $response->json();
            $userId = $data['id'] ?? null;
        }

        // Jika API Admin Supabase belum diizinkan, gunakan fallback register API biasa
        if (!$userId) {
            $signupResponse = Http::withHeaders([
                'apikey'        => env('SUPABASE_ANON_KEY'),
                'Content-Type'  => 'application/json',
            ])->post("{$baseUrl}/auth/v1/signup", [
                'email'    => $request->email,
                'password' => $request->password,
                'data'     => [
                    'name' => $request->name,
                    'role' => $role,
                ]
            ]);

            if ($signupResponse->successful()) {
                $signupData = $signupResponse->json();
                $userId = $signupData['user']['id'] ?? $signupData['id'] ?? null;
            }
        }

        // Jika pendaftaran Supabase gagal sama sekali
        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan akun di Supabase. Periksa koneksi internet/pendaftaran Supabase.');
        }

        // 2. SIMPAN / SYNC PROFIL DI TABEL PROFILES DATABASE LOCAL/POSTGRESQL
        DB::table('profiles')->updateOrInsert(
            ['id' => $userId],
            [
                'name'       => $request->name,
                'email'      => $request->email,
                'role'       => $role,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil terdaftar di Supabase dan sistem!');
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
            // Hapus dari auth.users
            try {
                DB::table('auth.users')->where('id', $user->id)->delete();
            } catch (\Exception $e) {
                // Ignore jika sudah terhapus cascade
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }
}