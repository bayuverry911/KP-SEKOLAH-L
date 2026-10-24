<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPengganti extends Model
{
    protected $table = 'jadwal_penggantis';

    protected $fillable = [
        'jadwal_id', 'tanggal', 'guru_id', 'nama_guru', 'keterangan', 'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id');
    }
}
