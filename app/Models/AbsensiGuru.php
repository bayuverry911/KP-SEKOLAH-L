<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiGuru extends Model
{
    use HasFactory;

    protected $table = 'absensi_gurus';

    protected $fillable = [
        'user_id',
        'nama_guru',
        'nuptk_nip',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
        'lat_masuk',
        'lng_masuk',
        'jarak_masuk',
        'lat_keluar',
        'lng_keluar',
        'jarak_keluar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}