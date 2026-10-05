<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'nuptk',           // Diubah dari 'nip' menjadi 'nuptk'
        'jenis_kelamin',
        'mata_pelajaran',  // Atau 'mapel_id' jika menggunakan foreign key
        'no_telepon',
        'kelas',
    ];

    // Mengubah kolom 'kelas' dari teks "7,8,9" atau JSON menjadi array PHP otomatis
    protected $casts = [
        'kelas' => 'array',
    ];

    /**
     * Relasi ke Model User (Akun Login)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Model Mapel (Mata Pelajaran)
     */
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mata_pelajaran_id');
    }
}