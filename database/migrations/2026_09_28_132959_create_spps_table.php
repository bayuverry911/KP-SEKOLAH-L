<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spps', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel siswas
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');

            $table->string('bulan');
            $table->integer('tahun');
            $table->decimal('nominal', 12, 2);
            $table->timestamp('tanggal_bayar');
            
            // KOLOM BARU: Metode Pembayaran
            $table->string('metode_pembayaran')->default('Tunai');
            
            $table->string('status')->default('Lunas');
            $table->text('keterangan')->nullable();

            // Relasi ke admin (profiles)
            $table->uuid('admin_id');
            $table->foreign('admin_id')->references('id')->on('profiles')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spps');
    }
};