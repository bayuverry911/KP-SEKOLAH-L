<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id',
        'nama_guru',
        'kelas',
        'mapel',
        'hari',
        'jam_masuk',
        'jam_selesai',
    ];

    public function penggantis()
    {
        return $this->hasMany(JadwalPengganti::class, 'jadwal_id');
    }
}
