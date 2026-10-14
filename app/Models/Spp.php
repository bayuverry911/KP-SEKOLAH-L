<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\User;

class Spp extends Model
{
    use HasFactory;

    protected $table = 'spps';

    protected $fillable = [
        'no_transaksi',
        'siswa_id',
        'bulan',
        'tahun',
        'nominal',
        'metode_pembayaran',
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

    /**
     * Membuat nomor transaksi/kuitansi unik, contoh: SPP-20261008-000012.
     * Urutan diambil dari SEQUENCE PostgreSQL (aman dari bentrok & tidak dipakai ulang).
     */
    public static function generateNomor(): string
    {
        $urut = DB::selectOne("SELECT nextval('spp_no_transaksi_seq') AS n")->n;

        return 'SPP-' . now('Asia/Jakarta')->format('Ymd') . '-' . str_pad((string) $urut, 6, '0', STR_PAD_LEFT);
    }
}
