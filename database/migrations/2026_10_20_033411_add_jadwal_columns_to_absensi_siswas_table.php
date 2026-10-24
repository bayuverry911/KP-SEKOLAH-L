<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_siswas', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi_siswas', 'jadwal_id')) {
                $table->unsignedBigInteger('jadwal_id')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('absensi_siswas', 'is_pengganti')) {
                $table->boolean('is_pengganti')->default(false)->after('jadwal_id');
            }
        });

        // Cegah absen ganda (mis. dobel klik) untuk siswa + jadwal + tanggal yang sama.
        // Data lama (jadwal_id NULL) tidak ikut dibatasi.
        DB::statement(
            'CREATE UNIQUE INDEX IF NOT EXISTS absensi_siswas_jadwal_siswa_tanggal_unique
             ON absensi_siswas (jadwal_id, siswa_id, tanggal) WHERE jadwal_id IS NOT NULL'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS absensi_siswas_jadwal_siswa_tanggal_unique');

        Schema::table('absensi_siswas', function (Blueprint $table) {
            $table->dropColumn(['jadwal_id', 'is_pengganti']);
        });
    }
};
