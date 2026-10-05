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
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable()->constrained('profiles')->onDelete('cascade');
            $table->string('nama');
            $table->string('nuptk')->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('mata_pelajaran');
            $table->string('no_telepon')->nullable();
            $table->string('kelas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};