<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Absensi Utama (jam ke-1): sekali per kelas per hari, terpisah dari absensi mapel.
     */
    public function up(): void
    {
        Schema::create('absensi_utamas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kelas');
            $table->unsignedBigInteger('siswa_id');
            $table->string('status');                       // MASUK | IZIN | SAKIT | ABSEN
            $table->uuid('user_id')->nullable();            // guru yang menyelesaikan
            $table->unsignedBigInteger('jadwal_id')->nullable();
            $table->boolean('is_pengganti')->default(false);
            $table->timestamps();

            // Satu siswa hanya sekali per hari -> form terkunci setelah submit
            $table->unique(['kelas', 'siswa_id', 'tanggal']);
            $table->index(['kelas', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_utamas');
    }
};
