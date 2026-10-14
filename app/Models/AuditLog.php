<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LogicException;

/**
 * Catatan audit. Bersifat append-only: tidak boleh diubah / dihapus lewat aplikasi.
 */
class AuditLog extends Model
{
    const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'user_name', 'modul', 'aksi',
        'auditable_type', 'auditable_id', 'referensi',
        'data_lama', 'data_baru', 'alasan',
        'ip_address', 'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'data_lama'  => 'array',
            'data_baru'  => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Audit log tidak dapat diubah.');
        });

        static::deleting(function () {
            throw new LogicException('Audit log tidak dapat dihapus.');
        });
    }

    /**
     * Catat satu aktivitas beserta siapa, kapan, dan dari mana.
     */
    public static function catat(
        string $modul,
        string $aksi,
        Model $model,
        ?array $dataLama,
        ?array $dataBaru,
        ?string $referensi = null
    ): self {
        $user = Auth::user();

        return static::create([
            'user_id'        => Auth::id(),
            'user_name'      => $user->name ?? null,
            'modul'          => $modul,
            'aksi'           => $aksi,
            'auditable_type' => $model::class,
            'auditable_id'   => (string) $model->getKey(),
            'referensi'      => $referensi,
            'data_lama'      => $dataLama,
            'data_baru'      => $dataBaru,
            'alasan'         => request()->input('alasan'),
            'ip_address'     => request()->ip(),
            'user_agent'     => Str::limit((string) request()->userAgent(), 250, ''),
        ]);
    }
}