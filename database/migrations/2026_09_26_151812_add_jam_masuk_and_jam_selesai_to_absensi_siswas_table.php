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
        Schema::table('absensi_siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('absensi_siswas', 'jam_masuk')) {
                $table->string('jam_masuk')->nullable()->after('status');
            }
            if (!Schema::hasColumn('absensi_siswas', 'jam_selesai')) {
                $table->string('jam_selesai')->nullable()->after('jam_masuk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_siswas', function (Blueprint $table) {
            if (Schema::hasColumn('absensi_siswas', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }
            if (Schema::hasColumn('absensi_siswas', 'jam_selesai')) {
                $table->dropColumn('jam_selesai');
            }
        });
    }
};