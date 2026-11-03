<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin_gurus', function (Blueprint $table) {
            $table->id();

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('profiles')->onDelete('cascade');

            $table->string('jenis', 10);                     // Sakit | Dinas
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('keterangan');

            $table->string('bukti_path');                    // disk privat (storage/app)
            $table->string('bukti_nama')->nullable();

            $table->string('status', 15)->default('menunggu'); // menunggu | disetujui | ditolak
            $table->text('catatan_admin')->nullable();
            $table->uuid('diproses_oleh')->nullable();
            $table->timestamp('diproses_pada')->nullable();

            $table->timestamps();

            $table->index(['status', 'tanggal_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin_gurus');
    }
};
