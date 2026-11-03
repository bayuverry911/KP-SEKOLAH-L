<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasSiswa extends Model
{
    protected $table = 'kelas_siswas';

    protected $fillable = ['siswa_id', 'kelas_id', 'tahun_pelajaran_id', 'status'];
}
