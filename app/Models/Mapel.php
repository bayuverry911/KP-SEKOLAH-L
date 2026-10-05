<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    // Nama tabel di database (opsional, default 'mapels')
    protected $table = 'mapels';

    // Kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'nama_mapel',
    ];
}