<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_liburs';

    protected $fillable = ['tanggal', 'keterangan', 'jenis'];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }
}
