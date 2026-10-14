<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Tanpa foreign key: log harus tetap ada walaupun akun petugas dihapus
            $table->uuid('user_id')->nullable()->index();
            $table->string('user_name')->nullable();

            $table->string('modul', 50)->index();          // contoh: spp
            $table->string('aksi', 30)->index();           // dibuat | diubah | dihapus
            $table->string('auditable_type')->nullable();
            $table->string('auditable_id')->nullable();
            $table->string('referensi')->nullable()->index(); // contoh: nomor transaksi SPP

            $table->jsonb('data_lama')->nullable();
            $table->jsonb('data_baru')->nullable();
            $table->text('alasan')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
