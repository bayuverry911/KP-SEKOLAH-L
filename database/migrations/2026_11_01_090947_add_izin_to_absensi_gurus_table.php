<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->unsignedBigInteger('izin_id')->nullable()->after('keterangan');
            $table->boolean('terkunci')->default(false)->after('izin_id'); // izin disetujui -> presensi terkunci
        });
    }

    public function down(): void
    {
        Schema::table('absensi_gurus', function (Blueprint $table) {
            $table->dropColumn(['izin_id', 'terkunci']);
        });
    }
};
