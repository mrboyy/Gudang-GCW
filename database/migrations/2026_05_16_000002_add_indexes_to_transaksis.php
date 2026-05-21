<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Kolom-kolom ini sering dipakai di WHERE tapi belum ada index
            $table->index('no_ref',            'idx_transaksis_no_ref');
            $table->index('jenis_transaksi',   'idx_transaksis_jenis');
            $table->index('tanggal',            'idx_transaksis_tanggal');
            $table->index('is_void',            'idx_transaksis_is_void');
            $table->index(['id_barang', 'jenis_transaksi'], 'idx_transaksis_barang_jenis');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex('idx_transaksis_no_ref');
            $table->dropIndex('idx_transaksis_jenis');
            $table->dropIndex('idx_transaksis_tanggal');
            $table->dropIndex('idx_transaksis_is_void');
            $table->dropIndex('idx_transaksis_barang_jenis');
        });
    }
};
