<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor transaksi/kuitansi SPP.
     * Memakai SEQUENCE PostgreSQL supaya nomor unik, aman dari bentrok,
     * dan tidak pernah dipakai ulang walaupun data dihapus.
     */
    public function up(): void
    {
        DB::statement('CREATE SEQUENCE IF NOT EXISTS spp_no_transaksi_seq START 1');

        Schema::table('spps', function (Blueprint $table) {
            $table->string('no_transaksi', 30)->nullable()->after('id');
        });

        // Beri nomor untuk data SPP lama (urut sesuai ID)
        DB::table('spps')->whereNull('no_transaksi')->orderBy('id')->get(['id', 'tanggal_bayar'])
            ->each(function ($row) {
                $urut = DB::selectOne("SELECT nextval('spp_no_transaksi_seq') AS n")->n;
                $tanggal = date('Ymd', strtotime((string) $row->tanggal_bayar));

                DB::table('spps')->where('id', $row->id)->update([
                    'no_transaksi' => 'SPP-' . $tanggal . '-' . str_pad((string) $urut, 6, '0', STR_PAD_LEFT),
                ]);
            });

        Schema::table('spps', function (Blueprint $table) {
            $table->unique('no_transaksi');
        });
    }

    public function down(): void
    {
        Schema::table('spps', function (Blueprint $table) {
            $table->dropUnique(['no_transaksi']);
            $table->dropColumn('no_transaksi');
        });

        DB::statement('DROP SEQUENCE IF EXISTS spp_no_transaksi_seq');
    }
};
