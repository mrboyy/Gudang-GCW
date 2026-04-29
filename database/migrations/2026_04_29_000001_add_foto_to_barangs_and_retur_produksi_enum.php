<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('deskripsi');
        });

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar','retur_customer','retur_produksi') NOT NULL");
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('foto');
        });

        DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar','retur_customer') NOT NULL");
    }
};
