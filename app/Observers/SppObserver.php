<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Spp;
use Illuminate\Support\Arr;

/**
 * Mencatat SETIAP pembuatan, perubahan, dan penghapusan data SPP ke audit_logs,
 * dari mana pun perubahan itu berasal (controller, tinker, seeder, dll).
 * Berjalan di dalam transaksi yang sama: jika log gagal tersimpan, perubahannya ikut dibatalkan.
 */
class SppObserver
{
    public function created(Spp $spp): void
    {
        AuditLog::catat('spp', 'dibuat', $spp, null, $this->snapshot($spp), $spp->no_transaksi);
    }

    public function updated(Spp $spp): void
    {
        $perubahan = Arr::except($spp->getChanges(), ['updated_at']);

        if (empty($perubahan)) {
            return;
        }

        $lama = Arr::only($spp->getOriginal(), array_keys($perubahan));

        AuditLog::catat('spp', 'diubah', $spp, $lama, $perubahan, $spp->no_transaksi);
    }

    public function deleted(Spp $spp): void
    {
        AuditLog::catat('spp', 'dihapus', $spp, $this->snapshot($spp), null, $spp->no_transaksi);
    }

    private function snapshot(Spp $spp): array
    {
        $siswa = $spp->siswa;

        return array_merge(
            Arr::except($spp->getAttributes(), ['created_at', 'updated_at']),
            ['siswa' => $siswa ? $siswa->nisn . ' - ' . $siswa->nama : null]
        );
    }
}
