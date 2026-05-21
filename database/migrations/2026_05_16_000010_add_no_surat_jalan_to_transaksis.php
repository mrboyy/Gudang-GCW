<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->string('no_surat_jalan', 20)->nullable()->unique()->after('no_transaksi');
        });

        // Generate No. SJ untuk semua keluar yang sudah ada
        $keluars = DB::table('transaksis')
            ->where('jenis_transaksi', 'keluar')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'created_at']);

        $yearCounters = [];
        foreach ($keluars as $k) {
            $year = date('Y', strtotime($k->created_at));
            $yearCounters[$year] = ($yearCounters[$year] ?? 0) + 1;
            $seq = str_pad($yearCounters[$year], 4, '0', STR_PAD_LEFT);
            DB::table('transaksis')->where('id', $k->id)->update([
                'no_surat_jalan' => "SJ/{$year}/{$seq}",
            ]);
        }

        // Update no_ref di retur: ganti BK-... jadi SJ-... yang baru
        $returs = DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
            ->whereNotNull('no_ref')
            ->get(['id', 'no_ref']);

        foreach ($returs as $retur) {
            $sj = DB::table('transaksis')
                ->where('no_transaksi', $retur->no_ref)
                ->where('jenis_transaksi', 'keluar')
                ->value('no_surat_jalan');

            if ($sj) {
                DB::table('transaksis')->where('id', $retur->id)->update(['no_ref' => $sj]);
            }
        }
    }

    public function down(): void
    {
        // Kembalikan no_ref retur dari SJ ke BK
        $returs = DB::table('transaksis')
            ->whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
            ->whereNotNull('no_ref')
            ->get(['id', 'no_ref']);

        foreach ($returs as $retur) {
            $bk = DB::table('transaksis')
                ->where('no_surat_jalan', $retur->no_ref)
                ->where('jenis_transaksi', 'keluar')
                ->value('no_transaksi');

            if ($bk) {
                DB::table('transaksis')->where('id', $retur->id)->update(['no_ref' => $bk]);
            }
        }

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('no_surat_jalan');
        });
    }
};
