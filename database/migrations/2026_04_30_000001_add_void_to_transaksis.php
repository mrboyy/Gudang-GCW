<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->boolean('is_void')->default(false)->after('keterangan');
            $table->foreignId('void_by')->nullable()->after('is_void')->constrained('users')->nullOnDelete();
            $table->timestamp('void_at')->nullable()->after('void_by');
            $table->string('void_reason', 500)->nullable()->after('void_at');
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropForeign(['void_by']);
            $table->dropColumn(['is_void', 'void_by', 'void_at', 'void_reason']);
        });
    }
};
