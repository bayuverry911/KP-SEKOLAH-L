<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Nama tabel di database (opsional, default 'kelas' / 'kelases')
    protected $table = 'kelas';

    // Kolom yang dapat diisi secara massal (mass assignment)
    protected $fillable = [
        'nama_kelas',
    ];
}