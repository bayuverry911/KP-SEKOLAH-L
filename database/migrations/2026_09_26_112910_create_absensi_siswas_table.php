<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->string('kelas');
            $table->string('mapel');
            $table->string('pertemuan_ke');
            $table->date('tanggal');
            $table->string('status'); // MASUK, SAKIT, IZIN, ABSEN
            $table->timestamps();

            // Index untuk mempercepat query rekap
            $table->index(['siswa_id', 'tanggal', 'mapel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_siswas');
    }
};