<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinGuru extends Model
{
    protected $table = 'izin_gurus';

    protected $fillable = [
        'user_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai', 'keterangan',
        'bukti_path', 'bukti_nama', 'status', 'catatan_admin', 'diproses_oleh', 'diproses_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'diproses_pada'   => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
