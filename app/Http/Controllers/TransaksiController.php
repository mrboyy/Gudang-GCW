<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['barang', 'user']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('barang', fn($q2) => $q2->where('nama_barang', 'like', "%{$request->search}%"))
                  ->orWhere('no_transaksi', 'like', "%{$request->search}%")
                  ->orWhere('nomor_lot', 'like', "%{$request->search}%");
            });
        }
        if ($request->jenis) {
            $query->where('jenis_transaksi', $request->jenis);
        }
        if ($request->tanggal_dari) {
            $query->where('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->tanggal_sampai) {
            $query->where('tanggal', '<=', $request->tanggal_sampai);
        }

        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('transaksi.index', compact('transaksis'));
    }

    public function createMasuk()
    {
        $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        return view('transaksi.create-masuk', compact('barangs'));
    }

    public function createKeluar()
    {
        $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        return view('transaksi.create-keluar', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'       => 'required|exists:barangs,id',
            'jenis_transaksi' => 'required|in:masuk,keluar,retur_customer',
            'quantity'        => 'required|integer|min:1',
            'tanggal'         => 'required|date',
            'nomor_lot'       => 'nullable|string|max:100',
            'keterangan'      => 'nullable|string',
        ], [
            'id_barang.required' => 'Barang wajib dipilih',
            'quantity.required'  => 'Jumlah wajib diisi',
            'quantity.min'       => 'Jumlah minimal 1',
            'tanggal.required'   => 'Tanggal wajib diisi',
        ]);

        $barang = Barang::findOrFail($request->id_barang);
        $jenis  = $request->jenis_transaksi;

        if ($jenis === 'keluar') {
            $stokQuery = Stok::where('id_barang', $barang->id);
            if ($request->nomor_lot) {
                $stokQuery->where('nomor_lot', $request->nomor_lot);
            }
            $stokTotal = $stokQuery->sum('stok_akhir');
            if ($stokTotal < $request->quantity) {
                return back()->withErrors(['quantity' => "Stok tidak mencukupi. Stok tersedia: {$stokTotal} {$barang->satuan}"])->withInput();
            }
        }

        DB::transaction(function () use ($request, $barang, $jenis) {
            $prefixMap   = ['masuk' => 'BM', 'keluar' => 'BK', 'retur_customer' => 'RC'];
            $prefix      = $prefixMap[$jenis];
            $today       = now()->format('Ymd');
            $count       = Transaksi::where('no_transaksi', 'like', "{$prefix}-{$today}-%")
                ->lockForUpdate()->count() + 1;
            $noTransaksi = $prefix . '-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            Transaksi::create([
                'no_transaksi'    => $noTransaksi,
                'id_barang'       => $request->id_barang,
                'id_user'         => auth()->user()->id,
                'jenis_transaksi' => $jenis,
                'tanggal'         => $request->tanggal,
                'quantity'        => $request->quantity,
                'nomor_lot'       => $request->nomor_lot ?: null,
                'keterangan'      => $request->keterangan,
            ]);

            // retur_customer → stok naik (sama seperti masuk)
            $stokJenis = ($jenis === 'retur_customer') ? 'masuk' : $jenis;
            $barang->updateStok($stokJenis, $request->quantity, $request->nomor_lot ?: null);
        });

        $routeMap = ['masuk' => 'transaksi.masuk', 'keluar' => 'transaksi.keluar', 'retur_customer' => 'retur.index'];
        return redirect()->route($routeMap[$jenis])->with('success', 'Transaksi berhasil disimpan & stok diperbarui');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'user']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function masuk(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'masuk');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('transaksi.masuk', compact('transaksis'));
    }

    public function keluar(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'keluar');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('transaksi.keluar', compact('transaksis'));
    }

    public function createRetur()
    {
        $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        return view('retur.create', compact('barangs'));
    }

    public function retur(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_customer');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('retur.index', compact('transaksis'));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', fn($q2) => $q2->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%"))
                  ->orWhere('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('nomor_lot', 'like', "%{$search}%");
            });
        }
        if ($request->periode === 'hari') {
            $query->whereDate('tanggal', today());
        } elseif ($request->periode === 'bulan') {
            $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
        } elseif ($request->periode === 'tahun') {
            $query->whereYear('tanggal', now()->year);
        } else {
            if ($request->tanggal_dari) $query->where('tanggal', '>=', $request->tanggal_dari);
            if ($request->tanggal_sampai) $query->where('tanggal', '<=', $request->tanggal_sampai);
        }
    }

    // Cek stok + daftar lot via AJAX
    public function cekStok(Request $request)
    {
        $barang = Barang::find($request->id_barang);
        if (!$barang) return response()->json(['stok' => 0, 'satuan' => '', 'lots' => []]);

        $stokQuery = Stok::where('id_barang', $barang->id);
        if ($request->nomor_lot) {
            $stokQuery->where('nomor_lot', $request->nomor_lot);
        }

        $lots = Stok::where('id_barang', $barang->id)
            ->where('stok_akhir', '>', 0)
            ->whereNotNull('nomor_lot')
            ->orderBy('nomor_lot')
            ->get(['nomor_lot', 'stok_akhir']);

        return response()->json([
            'stok'   => $stokQuery->sum('stok_akhir'),
            'satuan' => $barang->satuan,
            'lots'   => $lots,
        ]);
    }
}
