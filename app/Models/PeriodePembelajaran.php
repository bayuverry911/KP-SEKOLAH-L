<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePembelajaran extends Model
{
    protected $table = 'periode_pembelajarans';

    protected $fillable = ['nama', 'tanggal_mulai', 'tanggal_selesai'];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
        ];
    }
}
