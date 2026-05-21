<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // nama_supplier di transaksis — ada di model & controller tapi belum ada migrasi
        if (!Schema::hasColumn('transaksis', 'nama_supplier')) {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->string('nama_supplier')->nullable()->after('tujuan_keluar');
            });
        }

        // phone & wa_api_key di users — ada di fillable tapi belum ada migrasi
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'wa_api_key')) {
                $table->string('wa_api_key')->nullable()->after('phone');
            }
        });

        // merk di barangs — migration awal tidak nullable, tapi form tidak wajib isi
        if (Schema::hasColumn('barangs', 'merk')) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->string('merk')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumn('transaksis', 'nama_supplier')) {
                $table->dropColumn('nama_supplier');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone'))      $table->dropColumn('phone');
            if (Schema::hasColumn('users', 'wa_api_key')) $table->dropColumn('wa_api_key');
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->string('merk')->nullable(false)->change();
        });
    }
};
