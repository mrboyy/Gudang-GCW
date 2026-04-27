<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barangs')->onDelete('cascade');
            $table->string('nomor_lot')->nullable();
            $table->integer('stok_akhir')->default(0);
            $table->timestamp('tanggal_update')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
