<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_penggantis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwals')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('guru_id');             // gurus.id guru pengganti
            $table->string('nama_guru');
            $table->text('keterangan')->nullable();
            $table->uuid('dibuat_oleh')->nullable();
            $table->timestamps();

            // Satu jadwal hanya boleh punya satu guru pengganti per tanggal
            $table->unique(['jadwal_id', 'tanggal']);
            $table->index(['tanggal', 'guru_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_penggantis');
    }
};
