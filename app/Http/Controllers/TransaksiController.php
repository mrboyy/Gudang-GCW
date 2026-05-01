<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
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

    public function createRetur()
    {
        $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        return view('retur.create', compact('barangs'));
    }

    public function createReturProduksi()
    {
        $barangs = Barang::where('is_active', true)->orderBy('nama_barang')->get();
        return view('retur.create-produksi', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang'       => 'required|exists:barangs,id',
            'jenis_transaksi' => 'required|in:masuk,keluar,retur_customer,retur_produksi',
            'quantity'        => 'required|integer|min:1|max:999999',
            'tanggal'         => 'required|date|before_or_equal:today',
            'nomor_lot'       => 'nullable|string|max:100',
            'keterangan'      => 'nullable|string|max:500',
        ], [
            'id_barang.required'      => 'Barang wajib dipilih',
            'id_barang.exists'        => 'Barang tidak valid',
            'quantity.required'       => 'Jumlah wajib diisi',
            'quantity.min'            => 'Jumlah minimal 1',
            'quantity.max'            => 'Jumlah terlalu besar',
            'tanggal.required'        => 'Tanggal wajib diisi',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan',
        ]);

        $barang = Barang::where('id', $request->id_barang)->where('is_active', true)->firstOrFail();
        $jenis  = $request->jenis_transaksi;

        $stokError = null;

        DB::transaction(function () use ($request, $barang, $jenis, &$stokError) {
            // Validasi stok di dalam transaksi dengan lock agar tidak ada race condition
            if ($jenis === 'keluar') {
                $stokQuery = Stok::where('id_barang', $barang->id)->lockForUpdate();
                if ($request->nomor_lot) {
                    $stokQuery->where('nomor_lot', $request->nomor_lot);
                }
                $stokTotal = $stokQuery->sum('stok_akhir');
                if ($stokTotal < $request->quantity) {
                    $stokError = "Stok tidak mencukupi. Stok tersedia: {$stokTotal} {$barang->satuan}";
                    return; // rollback otomatis saat exception — tapi kita return dulu
                }
            }

            $prefixMap   = ['masuk' => 'BM', 'keluar' => 'BK', 'retur_customer' => 'RC', 'retur_produksi' => 'RP'];
            $prefix      = $prefixMap[$jenis];
            $today       = now()->format('Ymd');
            $count       = Transaksi::where('no_transaksi', 'like', "{$prefix}-{$today}-%")
                ->lockForUpdate()->count() + 1;
            $noTransaksi = $prefix . '-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            Transaksi::create([
                'no_transaksi'    => $noTransaksi,
                'id_barang'       => $barang->id,
                'id_user'         => auth()->id(),
                'jenis_transaksi' => $jenis,
                'tanggal'         => $request->tanggal,
                'quantity'        => $request->quantity,
                'nomor_lot'       => $request->nomor_lot ?: null,
                'keterangan'      => $request->keterangan,
            ]);

            // retur → stok naik (sama seperti masuk)
            $stokJenis = in_array($jenis, ['retur_customer', 'retur_produksi']) ? 'masuk' : $jenis;
            $barang->updateStok($stokJenis, $request->quantity, $request->nomor_lot ?: null);
        });

        if ($stokError) {
            return back()->withErrors(['quantity' => $stokError])->withInput();
        }

        // Log setelah transaction selesai
        $saved = Transaksi::where('id_barang', $barang->id)
            ->where('jenis_transaksi', $jenis)
            ->latest()->first();
        if ($saved) {
            $jenisLabel = ['masuk'=>'Barang Masuk','keluar'=>'Barang Keluar','retur_customer'=>'Retur Customer','retur_produksi'=>'Retur Produksi'][$jenis] ?? $jenis;
            AuditLog::log('create', "Catat {$jenisLabel}: {$barang->nama_barang} — {$saved->quantity} {$barang->satuan} [{$saved->no_transaksi}]", $saved, [], [
                'no_transaksi'    => $saved->no_transaksi,
                'jenis_transaksi' => $jenis,
                'barang'          => $barang->nama_barang,
                'quantity'        => $saved->quantity,
                'nomor_lot'       => $saved->nomor_lot,
            ]);
        }

        Cache::forget('dashboard_stats');

        $routeMap = [
            'masuk'          => 'transaksi.masuk',
            'keluar'         => 'transaksi.keluar',
            'retur_customer' => 'retur.index',
            'retur_produksi' => 'retur.produksi.index',
        ];
        return redirect()->route($routeMap[$jenis])->with('success', 'Transaksi berhasil disimpan & stok diperbarui');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'user']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function printView(Transaksi $transaksi)
    {
        $transaksi->load(['barang', 'user', 'voidUser']);
        return view('transaksi.print', compact('transaksi'));
    }

    public function void(Request $request, Transaksi $transaksi)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isKepalaGudang()) {
            abort(403);
        }

        if ($transaksi->is_void) {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        $request->validate([
            'void_reason' => 'required|string|max:500',
        ], [
            'void_reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $transaksi) {
            $transaksi->barang->reverseStok(
                $transaksi->jenis_transaksi,
                $transaksi->quantity,
                $transaksi->nomor_lot
            );

            $transaksi->update([
                'is_void'     => true,
                'void_by'     => auth()->id(),
                'void_at'     => now(),
                'void_reason' => $request->void_reason,
            ]);
        });

        AuditLog::log('void', "Void transaksi {$transaksi->no_transaksi}: {$transaksi->barang->nama_barang} — {$request->void_reason}", $transaksi,
            ['is_void' => false],
            ['is_void' => true, 'void_reason' => $request->void_reason]
        );

        Cache::forget('dashboard_stats');

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikoreksi.');
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

    public function retur(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_customer');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('retur.index', compact('transaksis'));
    }

    public function returProduksi(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_produksi');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(15)->withQueryString();
        return view('retur.produksi', compact('transaksis'));
    }

    // Cek stok + daftar lot via AJAX (hanya barang aktif)
    public function cekStok(Request $request)
    {
        $barang = Barang::where('id', (int) $request->id_barang)->where('is_active', true)->first();
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
}
