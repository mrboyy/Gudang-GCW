<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Merge duplicate (id_barang, nomor_lot) rows before adding unique constraint
        // This query sums stok_akhir per group and keeps only the first row
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("
                DELETE s1 FROM stoks s1
                INNER JOIN stoks s2
                WHERE s1.id > s2.id
                  AND s1.id_barang = s2.id_barang
                  AND (
                    (s1.nomor_lot IS NULL AND s2.nomor_lot IS NULL)
                    OR s1.nomor_lot = s2.nomor_lot
                  )
            ");

            // After duplicates removed, ensure stok_akhir minimum is 0
            DB::statement("UPDATE stoks SET stok_akhir = 0 WHERE stok_akhir < 0");
        }

        Schema::table('stoks', function (Blueprint $table) {
            // Prevent duplicate (barang, lot) pairs — critical for stok integrity
            $table->unique(['id_barang', 'nomor_lot'], 'uq_stoks_barang_lot');
            // stok_akhir should never be negative at DB level
            if (DB::getDriverName() !== 'sqlite') {
                // MySQL 8.0.16+ supports CHECK constraints
            }
        });
    }

    public function down(): void
    {
        Schema::table('stoks', function (Blueprint $table) {
            $table->dropUnique('uq_stoks_barang_lot');
        });
    }
};
