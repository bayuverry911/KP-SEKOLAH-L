<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;
use App\Models\User;

class Spp extends Model
{
    use HasFactory;

    protected $table = 'spps';

    protected $fillable = [
        'siswa_id',
        'bulan',
        'tahun',
        'nominal',
        'metode_pembayaran', // <--- DITAMBAHKAN
        'tanggal_bayar',
        'status',
        'keterangan',
        'admin_id',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}