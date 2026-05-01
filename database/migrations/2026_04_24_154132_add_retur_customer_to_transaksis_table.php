<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL-only: alter ENUM column; skip on SQLite (used in testing)
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar','retur_customer') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transaksis MODIFY COLUMN jenis_transaksi ENUM('masuk','keluar') NOT NULL");
        }
    }
};
