<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat penempatan kelas siswa per Tahun Pelajaran.
     * Kolom siswas.kelas TIDAK ditimpa, sehingga data historis (absensi kelas lama) tetap utuh.
     */
    public function up(): void
    {
        Schema::create('kelas_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete(); // NULL bila lulus
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajarans')->cascadeOnDelete();
            $table->string('status', 10)->default('aktif');  // aktif | lulus
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_pelajaran_id']);
            $table->index(['tahun_pelajaran_id', 'kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_siswas');
    }
};
