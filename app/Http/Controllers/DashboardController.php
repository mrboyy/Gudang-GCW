<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stokMinimumSub = DB::raw('(SELECT COALESCE(SUM(stok_akhir), 0) FROM stoks WHERE id_barang = barangs.id)');

        $stats = Cache::remember('dashboard_stats', 60, function () use ($today, $thisMonth, $stokMinimumSub) {
            return [
                'total_barang'    => Barang::where('is_active', true)->count(),
                'masuk_hari_ini'  => Transaksi::whereIn('jenis_transaksi', ['masuk','retur_customer','retur_produksi'])->where('is_void', false)->whereDate('tanggal', $today)->count(),
                'keluar_hari_ini' => Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $today)->count(),
                'masuk_bulan_ini' => Transaksi::whereIn('jenis_transaksi', ['masuk','retur_customer','retur_produksi'])->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
                'keluar_bulan_ini'=> Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
                'stok_minimum'    => Barang::where('is_active', true)->whereColumn('stok_minimum', '>=', $stokMinimumSub)->count(),
            ];
        });

        $recentMasuk  = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'masuk')->where('is_void', false)->orderByDesc('created_at')->limit(5)->get();
        $recentKeluar = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'keluar')->where('is_void', false)->orderByDesc('created_at')->limit(5)->get();

        $barangList = Barang::where('is_active', true)
            ->withSum('stoks as stok_total', 'stok_akhir')
            ->orderByRaw('COALESCE((SELECT SUM(stok_akhir) FROM stoks WHERE id_barang = barangs.id), 0) ASC')
            ->limit(10)
            ->get();

        $barangStokMinimum = Barang::where('is_active', true)
            ->whereColumn('stok_minimum', '>=', $stokMinimumSub)
            ->withSum('stoks as stok_total', 'stok_akhir')
            ->get();

        // Data chart 7 hari terakhir
        $chartData = Cache::remember('dashboard_chart', 60, function () {
            $labels = [];
            $masuk  = [];
            $keluar = [];
            for ($i = 6; $i >= 0; $i--) {
                $date     = Carbon::today()->subDays($i);
                $labels[] = $date->locale('id')->isoFormat('ddd, D MMM');
                $masuk[]  = Transaksi::whereIn('jenis_transaksi', ['masuk', 'retur_customer', 'retur_produksi'])->where('is_void', false)->whereDate('tanggal', $date)->count();
                $keluar[] = Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $date)->count();
            }
            return compact('labels', 'masuk', 'keluar');
        });
        $chartLabels = $chartData['labels'];
        $chartMasuk  = $chartData['masuk'];
        $chartKeluar = $chartData['keluar'];

        return view('dashboard', compact(
            'stats', 'recentMasuk', 'recentKeluar', 'barangStokMinimum',
            'chartLabels', 'chartMasuk', 'chartKeluar', 'barangList'
        ));
    }

    public function lastUpdate()
    {
        $tsTransaksi = Transaksi::max('created_at');
        $tsBarang    = Barang::max('updated_at');
        $ts = max(
            $tsTransaksi ? strtotime($tsTransaksi) : 0,
            $tsBarang    ? strtotime($tsBarang)    : 0
        );
        return response()->json(['ts' => $ts]);
    }

}
