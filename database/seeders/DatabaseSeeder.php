<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Membuat akun admin awal (karena login tidak lagi lewat Supabase).
     */
    public function run(): void
    {
        $email = 'admin@sekolah.test'; // GANTI sesuai kebutuhan

        if (! User::where('email', $email)->exists()) {
            User::create([
                'id'       => (string) Str::uuid(),
                'name'     => 'Administrator',
                'email'    => $email,
                'password' => 'Admin12345', // GANTI setelah login pertama (otomatis di-hash)
                'role'     => 'ADMIN',
            ]);
        }
    }
}
