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
                'masuk_hari_ini'  => Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->whereDate('tanggal', $today)->count(),
                'keluar_hari_ini' => Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $today)->count(),
                'masuk_bulan_ini' => Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
                'keluar_bulan_ini'=> Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
                'stok_minimum'    => Barang::where('is_active', true)->whereColumn('stok_minimum', '>=', $stokMinimumSub)->count(),
            ];
        });

        $recentMasuk  = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'masuk')->where('is_void', false)->orderByDesc('created_at')->limit(5)->get();
        $recentKeluar = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'keluar')->where('is_void', false)->orderByDesc('created_at')->limit(5)->get();

        $barangStokMinimum = Barang::where('is_active', true)
            ->whereColumn('stok_minimum', '>=', $stokMinimumSub)
            ->withSum('stoks as stok_total', 'stok_akhir')
            ->get();

        // Data chart 7 hari terakhir
        $chartLabels = [];
        $chartMasuk  = [];
        $chartKeluar = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->locale('id')->isoFormat('ddd, D MMM');
            $chartMasuk[]  = Transaksi::whereIn('jenis_transaksi', ['masuk', 'retur_customer', 'retur_produksi'])->where('is_void', false)->whereDate('tanggal', $date)->count();
            $chartKeluar[] = Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $date)->count();
        }

        return view('dashboard', compact(
            'stats', 'recentMasuk', 'recentKeluar', 'barangStokMinimum',
            'chartLabels', 'chartMasuk', 'chartKeluar'
        ));
    }

    public function lastUpdate()
    {
        $ts = Transaksi::max('created_at');
        return response()->json(['ts' => $ts ? strtotime($ts) : 0]);
    }

}
