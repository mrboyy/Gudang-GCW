<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stokMinimumSub = DB::raw('(SELECT COALESCE(SUM(stok_akhir), 0) FROM stoks WHERE id_barang = barangs.id)');

        $stats = [
            'total_barang'    => Barang::where('is_active', true)->count(),
            'masuk_hari_ini'  => Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->whereDate('tanggal', $today)->count(),
            'keluar_hari_ini' => Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $today)->count(),
            'masuk_bulan_ini' => Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
            'keluar_bulan_ini'=> Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->where('tanggal', '>=', $thisMonth)->count(),
            'stok_minimum'    => Barang::where('is_active', true)->whereColumn('stok_minimum', '>=', $stokMinimumSub)->count(),
        ];

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
            $chartLabels[] = $date->locale('id')->isoFormat('ddd D/M');
            $chartMasuk[]  = Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->whereDate('tanggal', $date)->sum('quantity') ?? 0;
            $chartKeluar[] = Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $date)->sum('quantity') ?? 0;
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

    public function live()
    {
        $today          = Carbon::today();
        $stokMinimumSub = DB::raw('(SELECT COALESCE(SUM(stok_akhir), 0) FROM stoks WHERE id_barang = barangs.id)');

        $stats = [
            'total_barang'    => Barang::where('is_active', true)->count(),
            'masuk_hari_ini'  => Transaksi::where('jenis_transaksi', 'masuk')->where('is_void', false)->whereDate('tanggal', $today)->count(),
            'keluar_hari_ini' => Transaksi::where('jenis_transaksi', 'keluar')->where('is_void', false)->whereDate('tanggal', $today)->count(),
            'stok_minimum'    => Barang::where('is_active', true)->whereColumn('stok_minimum', '>=', $stokMinimumSub)->count(),
        ];

        $recentMasuk = Transaksi::with('barang')
            ->where('jenis_transaksi', 'masuk')->where('is_void', false)
            ->orderByDesc('created_at')->limit(5)->get()
            ->map(fn($t) => [
                'no_transaksi' => $t->no_transaksi,
                'nama_barang'  => $t->barang->nama_barang,
                'quantity'     => $t->quantity,
                'tanggal'      => $t->tanggal->format('d/m/Y'),
            ]);

        $recentKeluar = Transaksi::with('barang')
            ->where('jenis_transaksi', 'keluar')->where('is_void', false)
            ->orderByDesc('created_at')->limit(5)->get()
            ->map(fn($t) => [
                'no_transaksi' => $t->no_transaksi,
                'nama_barang'  => $t->barang->nama_barang,
                'quantity'     => $t->quantity,
                'tanggal'      => $t->tanggal->format('d/m/Y'),
            ]);

        return response()->json(compact('stats', 'recentMasuk', 'recentKeluar'));
    }
}
