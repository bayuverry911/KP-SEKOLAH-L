<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials via Supabase.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $baseUrl = rtrim(env('SUPABASE_URL'), '/');
        $anonKey = env('SUPABASE_ANON_KEY');

        // 1. Kirim HTTP POST dengan header apikey DAN Authorization Bearer
        $response = Http::withHeaders([
            'apikey'        => $anonKey,
            'Authorization' => 'Bearer ' . $anonKey,
            'Content-Type'  => 'application/json',
        ])->post("{$baseUrl}/auth/v1/token?grant_type=password", [
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        // Jika Supabase menolak autentikasi
        if ($response->failed()) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $data = $response->json();
        $supabaseUserId = $data['user']['id'] ?? null;

        if (!$supabaseUserId) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // 2. Cari data profil di tabel 'profiles' berdasarkan ID Supabase atau email
        $user = User::where('id', $supabaseUserId)->orWhere('email', $this->email)->first();

        // --- AUTO-SYNC PROFIL ---
        // Jika profil belum ada di tabel profiles (misal setelah migrate:fresh)
        if (!$user) {
            // Tentukan role otomatis berdasarkan email (Admin jika email mengandung 'admin')
            $defaultRole = str_contains(strtolower($this->email), 'admin') ? 'admin' : 'guru';

            // Ambil display name jika ada di user_metadata Supabase
            $userName = $data['user']['user_metadata']['name']
                        ?? $data['user']['user_metadata']['full_name']
                        ?? explode('@', $this->email)[0];

            // Buat profil otomatis di database lokal
            $user = User::create([
                'id'    => $supabaseUserId,
                'email' => $this->email,
                'name'  => $userName,
                'role'  => $defaultRole,
            ]);
        }

        // 3. Login-kan user ke sesi Laravel
        Auth::login($user, $this->boolean('remember'));
    }
}