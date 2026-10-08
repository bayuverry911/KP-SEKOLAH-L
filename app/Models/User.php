<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Tabel profiles (akun login, disimpan di PostgreSQL lokal)
    protected $table = 'profiles';

    // Primary Key berbentuk UUID string
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'email',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Helper method untuk cek apakah user adalah Admin
     */
    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }

    /**
     * Helper method untuk cek apakah user adalah Guru
     */
    public function isGuru(): bool
    {
        return strtolower((string) $this->role) === 'guru';
    }

    /**
     * Relasi ke tabel Guru
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id', 'id');
    }
}