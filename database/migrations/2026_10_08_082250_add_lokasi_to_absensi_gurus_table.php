<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan bukti lokasi GPS saat guru absen masuk & pulang.
     */
    public function up(): void
    {
        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->decimal('lat_masuk', 10, 7)->nullable();
            $table->decimal('lng_masuk', 10, 7)->nullable();
            $table->unsignedInteger('jarak_masuk')->nullable();   // meter dari sekolah

            $table->decimal('lat_keluar', 10, 7)->nullable();
            $table->decimal('lng_keluar', 10, 7)->nullable();
            $table->unsignedInteger('jarak_keluar')->nullable();  // meter dari sekolah
        });
    }

    public function down(): void
    {
        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->dropColumn([
                'lat_masuk', 'lng_masuk', 'jarak_masuk',
                'lat_keluar', 'lng_keluar', 'jarak_keluar',
            ]);
        });
    }
};
