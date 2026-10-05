<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('guru_id')->nullable();
            $table->string('nama_guru')->nullable();
            $table->string('kelas')->nullable(); // misal: "7A", "8A"
            $table->string('mapel')->nullable(); // misal: "Bahasa Indonesia"
            $table->string('hari')->nullable();  // misal: "Senin", "Selasa"
            $table->time('jam_masuk')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};