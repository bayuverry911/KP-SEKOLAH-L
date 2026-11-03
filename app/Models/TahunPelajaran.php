<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunPelajaran extends Model
{
    protected $table = 'tahun_pelajarans';

    protected $fillable = ['nama', 'tanggal_mulai', 'tanggal_selesai', 'aktif'];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
            'aktif'           => 'boolean',
        ];
    }

    public static function aktif(): ?self
    {
        return static::where('aktif', true)->first();
    }
}
