<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('jenis_transaksi');
            $table->index('is_void');
            $table->index(['id_barang', 'tanggal']);
            $table->index(['jenis_transaksi', 'is_void', 'tanggal']);
        });

        Schema::table('stoks', function (Blueprint $table) {
            $table->index('stok_akhir');
            $table->index('tanggal_update');
            $table->index(['id_barang', 'stok_akhir']);
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['jenis_transaksi']);
            $table->dropIndex(['is_void']);
            $table->dropIndex(['id_barang', 'tanggal']);
            $table->dropIndex(['jenis_transaksi', 'is_void', 'tanggal']);
        });

        Schema::table('stoks', function (Blueprint $table) {
            $table->dropIndex(['stok_akhir']);
            $table->dropIndex(['tanggal_update']);
            $table->dropIndex(['id_barang', 'stok_akhir']);
        });
    }
};
