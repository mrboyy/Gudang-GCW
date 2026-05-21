<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\Stok;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function __construct(private StokService $stokService) {}

    public function createMasuk()
    {
        $barangs   = Barang::where('is_active', true)->orderByRaw('CAST(SUBSTRING(kode_barang, -3) AS UNSIGNED)')->get();
        $suppliers = Supplier::orderBy('nama')->pluck('nama');
        return view('transaksi.create-masuk', compact('barangs', 'suppliers'));
    }

    public function createKeluar()
    {
        $barangs = Barang::where('is_active', true)->orderByRaw('CAST(SUBSTRING(kode_barang, -3) AS UNSIGNED)')->get();
        return view('transaksi.create-keluar', compact('barangs'));
    }

    public function createReturCombined(Request $request)
    {
        $barangs = Barang::where('is_active', true)->orderByRaw('CAST(SUBSTRING(kode_barang, -3) AS UNSIGNED)')->get();

        $fullyCovered = $this->fullyCoveredReturIds();

        $transaksisCustomer = Transaksi::where('jenis_transaksi', 'keluar')
            ->where('is_void', false)
            ->where('tujuan_keluar', 'like', 'Customer%')
            ->whereNotIn('no_surat_jalan', $fullyCovered)
            ->with('barang')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->limit(500)->get();

        $transaksisInternal = Transaksi::where('jenis_transaksi', 'keluar')
            ->where('is_void', false)
            ->where('tujuan_keluar', 'like', 'Internal%')
            ->whereNotIn('no_surat_jalan', $fullyCovered)
            ->with('barang')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->limit(500)->get();

        if ($request->no_ref) {
            $noRef = $request->no_ref;
            foreach ([&$transaksisCustomer, &$transaksisInternal] as &$col) {
                if (!$col->contains('no_surat_jalan', $noRef)) {
                    $pinned = Transaksi::where('no_surat_jalan', $noRef)
                        ->where('jenis_transaksi', 'keluar')->where('is_void', false)
                        ->with('barang')->first();
                    if ($pinned) $col = $col->prepend($pinned);
                }
            }
        }

        $allSJs = $transaksisCustomer->pluck('no_surat_jalan')->merge($transaksisInternal->pluck('no_surat_jalan'));
        $returSums = $this->returSumsFor($allSJs->all());

        return view('retur.create-combined', compact('barangs', 'transaksisCustomer', 'transaksisInternal', 'returSums'));
    }

    public function createRetur(Request $request)
    {
        $barangs = Barang::where('is_active', true)->orderByRaw('CAST(SUBSTRING(kode_barang, -3) AS UNSIGNED)')->get();

        $transaksisKeluar = Transaksi::where('jenis_transaksi', 'keluar')
            ->where('is_void', false)
            ->where('tujuan_keluar', 'like', 'Customer%')
            ->whereNotIn('no_surat_jalan', $this->fullyCoveredReturIds())
            ->with('barang')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->limit(500)->get();

        if ($request->no_ref && !$transaksisKeluar->contains('no_surat_jalan', $request->no_ref)) {
            $pinned = Transaksi::where('no_surat_jalan', $request->no_ref)
                ->where('jenis_transaksi', 'keluar')->where('is_void', false)
                ->with('barang')->first();
            if ($pinned) $transaksisKeluar = $transaksisKeluar->prepend($pinned);
        }

        $returSums = $this->returSumsFor($transaksisKeluar->pluck('no_surat_jalan')->all());
        return view('retur.create', compact('barangs', 'transaksisKeluar', 'returSums'));
    }

    public function createReturProduksi(Request $request)
    {
        $barangs = Barang::where('is_active', true)->orderByRaw('CAST(SUBSTRING(kode_barang, -3) AS UNSIGNED)')->get();

        $transaksisKeluar = Transaksi::where('jenis_transaksi', 'keluar')
            ->where('is_void', false)
            ->where(function ($q) {
                $q->where('tujuan_keluar', 'like', 'Internal%')
                  ->orWhere('tujuan_keluar', 'like', 'Produksi%')
                  ->orWhere('tujuan_keluar', 'like', 'Departemen%');
            })
            ->whereNotIn('no_surat_jalan', $this->fullyCoveredReturIds())
            ->with('barang')
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->limit(500)->get();

        if ($request->no_ref && !$transaksisKeluar->contains('no_surat_jalan', $request->no_ref)) {
            $pinned = Transaksi::where('no_surat_jalan', $request->no_ref)
                ->where('jenis_transaksi', 'keluar')->where('is_void', false)
                ->with('barang')->first();
            if ($pinned) $transaksisKeluar = $transaksisKeluar->prepend($pinned);
        }

        $returSums = $this->returSumsFor($transaksisKeluar->pluck('no_surat_jalan')->all());
        return view('retur.create-produksi', compact('barangs', 'transaksisKeluar', 'returSums'));
    }

    private function fullyCoveredReturIds(): array
    {
        return DB::table('transaksis as r')
            ->select('r.no_ref')
            ->whereIn('r.jenis_transaksi', ['retur_customer', 'retur_produksi'])
            ->where('r.is_void', false)
            ->whereNotNull('r.no_ref')
            ->groupBy('r.no_ref')
            ->havingRaw('SUM(r.quantity) >= (SELECT k.quantity FROM transaksis k WHERE k.no_surat_jalan = r.no_ref LIMIT 1)')
            ->pluck('no_ref')
            ->all();
    }

    private function returSumsFor(array $noSJList): array
    {
        if (empty($noSJList)) return [];
        return Transaksi::whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
            ->where('is_void', false)
            ->whereIn('no_ref', $noSJList)
            ->groupBy('no_ref')
            ->selectRaw('no_ref, SUM(quantity) as total_retur')
            ->pluck('total_retur', 'no_ref')
            ->toArray();
    }

    public function store(Request $request)
    {
        $isRetur = in_array($request->jenis_transaksi, ['retur_customer', 'retur_produksi']);

        $lotRule   = $request->jenis_transaksi === 'keluar'
            ? 'required|string|max:100'
            : 'nullable|string|max:100';
        $noRefRule = $isRetur
            ? 'required|string|max:50'
            : 'nullable|string|max:50';

        $request->validate([
            'id_barang'       => 'required|exists:barangs,id',
            'jenis_transaksi' => 'required|in:masuk,keluar,retur_customer,retur_produksi',
            'quantity'        => 'required|integer|min:1|max:999999',
            'tanggal'         => 'required|date|before_or_equal:today',
            'nomor_lot'       => $lotRule,
            'keterangan'      => 'nullable|string|max:500',
            'tujuan_keluar'   => 'nullable|string|max:200',
            'no_ref'          => $noRefRule,
            'nama_supplier'   => 'nullable|string|max:200',
        ], [
            'id_barang.required'      => 'Barang wajib dipilih',
            'id_barang.exists'        => 'Barang tidak valid',
            'quantity.required'       => 'Jumlah wajib diisi',
            'quantity.min'            => 'Jumlah minimal 1',
            'quantity.max'            => 'Jumlah terlalu besar',
            'tanggal.required'        => 'Tanggal wajib diisi',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan',
            'nomor_lot.required'      => 'Nomor Lot wajib dipilih untuk barang keluar',
            'no_ref.required'         => 'No. Ref wajib dipilih — retur harus berdasarkan transaksi keluar',
        ]);

        $barang = Barang::where('id', $request->id_barang)->where('is_active', true)->firstOrFail();
        $jenis  = $request->jenis_transaksi;

        // Pre-validate retur: lot match + partial qty cap
        $refTx = null;
        if ($request->filled('no_ref') && in_array($jenis, ['retur_customer', 'retur_produksi'])) {
            $refTx = Transaksi::where('no_surat_jalan', $request->no_ref)
                ->where('jenis_transaksi', 'keluar')
                ->where('is_void', false)
                ->with('barang')
                ->first();
            if (!$refTx) {
                return back()->withInput()->withErrors(['no_ref' => 'No. Surat Jalan tidak ditemukan.']);
            }
            if ($refTx->nomor_lot && $request->nomor_lot !== $refTx->nomor_lot) {
                return back()->withInput()->withErrors(['nomor_lot' => 'Nomor Lot tidak sesuai dengan No. SJ ' . $request->no_ref . ' (Lot: ' . $refTx->nomor_lot . ')']);
            }
            $sudahDireturQty = Transaksi::whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
                ->where('no_ref', $request->no_ref)->where('is_void', false)->sum('quantity');
            $sisaQuota = $refTx->quantity - $sudahDireturQty;
            if ($request->quantity > $sisaQuota) {
                return back()->withInput()->withErrors(['quantity' => "Qty retur ({$request->quantity}) melebihi sisa quota. Sudah diretur: {$sudahDireturQty}, Sisa: {$sisaQuota} {$refTx->barang->satuan}."]);
            }
        }

        $stokError   = null;
        $returError  = null;
        $noTransaksi = null;

        try {
            DB::transaction(function () use ($request, $barang, $jenis, $refTx, &$stokError, &$returError, &$noTransaksi) {
                // Atomic partial-retur guard: sum existing + new <= original qty
                if (in_array($jenis, ['retur_customer', 'retur_produksi']) && $refTx) {
                    $existingTotal = Transaksi::whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
                        ->where('no_ref', $request->no_ref)
                        ->where('is_void', false)
                        ->lockForUpdate()
                        ->sum('quantity');
                    $sisa = $refTx->quantity - $existingTotal;
                    if ($request->quantity > $sisa) {
                        $returError = "Qty retur melebihi sisa quota. Sisa yang bisa diretur: {$sisa} {$refTx->barang->satuan}.";
                        return;
                    }
                }
                if ($jenis === 'keluar') {
                    $stokQuery = Stok::where('id_barang', $barang->id)->lockForUpdate();
                    if ($request->nomor_lot) {
                        $stokQuery->where('nomor_lot', $request->nomor_lot);
                    }
                    $stokTotal = $stokQuery->sum('stok_akhir');
                    if ($stokTotal < $request->quantity) {
                        $stokError = "Stok tidak mencukupi. Stok tersedia: {$stokTotal} {$barang->satuan}";
                        return;
                    }
                }

                $prefixMap   = ['masuk' => 'BM', 'keluar' => 'BK', 'retur_customer' => 'RC', 'retur_produksi' => 'RP'];
                $prefix      = $prefixMap[$jenis];
                $today       = now()->format('Ymd');
                $count       = Transaksi::where('no_transaksi', 'like', "{$prefix}-{$today}-%")
                    ->lockForUpdate()->count() + 1;
                $noTransaksi = $prefix . '-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                $noSuratJalan = null;
                if ($jenis === 'keluar') {
                    $year = now()->format('Y');
                    $sjCount = Transaksi::where('no_surat_jalan', 'like', "SJ/{$year}/%")
                        ->lockForUpdate()->count() + 1;
                    $noSuratJalan = 'SJ/' . $year . '/' . str_pad($sjCount, 4, '0', STR_PAD_LEFT);
                }

                Transaksi::create([
                    'no_transaksi'    => $noTransaksi,
                    'no_surat_jalan'  => $noSuratJalan,
                    'no_ref'          => $jenis === 'masuk' ? null : $request->no_ref,
                    'id_barang'       => $barang->id,
                    'id_user'         => auth()->id(),
                    'jenis_transaksi' => $jenis,
                    'tanggal'         => $request->tanggal,
                    'quantity'        => $request->quantity,
                    'nomor_lot'       => $request->nomor_lot ?: null,
                    'keterangan'      => $request->keterangan,
                    'tujuan_keluar'   => $request->tujuan_keluar,
                    'nama_supplier'   => $request->nama_supplier ?: null,
                ]);

                if (in_array($jenis, ['masuk', 'retur_customer', 'retur_produksi'])) {
                    $this->stokService->tambahStok($barang, $request->quantity, $request->nomor_lot ?: null);
                } else {
                    $this->stokService->kurangiStok($barang, $request->quantity, $request->nomor_lot ?: null);
                }
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withErrors(['quantity' => 'Nomor transaksi bentrok karena submit bersamaan. Silakan coba lagi.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['quantity' => $e->getMessage()])->withInput();
        }

        if ($returError) {
            return back()->withErrors(['no_ref' => $returError])->withInput();
        }

        if ($stokError) {
            return back()->withErrors(['quantity' => $stokError])->withInput();
        }

        // Audit log pakai no_transaksi yang pasti unik, bukan latest()
        $saved = $noTransaksi ? Transaksi::where('no_transaksi', $noTransaksi)->first() : null;
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

        // Auto-save supplier baru ke tabel suppliers
        if ($jenis === 'masuk' && $request->filled('nama_supplier')) {
            Supplier::simpanJikaBaru($request->nama_supplier);
        }

        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_chart');

        $routeMap = [
            'masuk'          => 'transaksi.masuk',
            'keluar'         => 'transaksi.keluar',
            'retur_customer' => 'retur.index',
            'retur_produksi' => 'retur.produksi.index',
        ];
        return redirect()->route($routeMap[$jenis])->with('success', 'Transaksi berhasil disimpan & stok diperbarui');
    }

    public function edit(Transaksi $transaksi)
    {
        if ($transaksi->is_void) abort(403, 'Transaksi yang sudah dibatalkan tidak dapat diedit.');
        $transaksi->load(['barang', 'user']);
        return view('transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->is_void) abort(403);

        $request->validate([
            'keterangan' => 'nullable|string|max:500',
            'tanggal'    => 'required|date|before_or_equal:today',
        ], [
            'tanggal.required'        => 'Tanggal wajib diisi',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan',
        ]);

        $old = $transaksi->only(['keterangan', 'tanggal']);

        $transaksi->update([
            'keterangan' => $request->keterangan,
            'tanggal'    => $request->tanggal,
        ]);

        AuditLog::log('update', "Edit transaksi {$transaksi->no_transaksi}: keterangan/tanggal diperbarui", $transaksi,
            $old,
            $transaksi->only(['keterangan', 'tanggal'])
        );

        return redirect()->route('transaksi.show', $transaksi)->with('success', 'Transaksi berhasil diperbarui.');
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

        // Cek apakah transaksi ini sudah punya retur aktif — void tidak bisa dilakukan dulu
        $hasActiveRetur = Transaksi::whereIn('jenis_transaksi', ['retur_customer', 'retur_produksi'])
            ->where('no_ref', $transaksi->no_surat_jalan ?? $transaksi->no_transaksi)
            ->where('is_void', false)
            ->exists();
        if ($hasActiveRetur) {
            return back()->with('error', 'Transaksi ini tidak bisa dibatalkan karena sudah memiliki retur aktif. Void retur terlebih dahulu.');
        }

        $request->validate([
            'void_reason' => 'required|string|max:500',
        ], [
            'void_reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($request, $transaksi) {
                $this->stokService->kembalikanStok(
                    $transaksi->barang,
                    $transaksi->quantity,
                    $transaksi->nomor_lot,
                    $transaksi->jenis_transaksi
                );

                $transaksi->update([
                    'is_void'     => true,
                    'void_by'     => auth()->id(),
                    'void_at'     => now(),
                    'void_reason' => $request->void_reason,
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }

        AuditLog::log('void', "Void transaksi {$transaksi->no_transaksi}: {$transaksi->barang->nama_barang} — {$request->void_reason}", $transaksi,
            ['is_void' => false],
            ['is_void' => true, 'void_reason' => $request->void_reason]
        );

        Cache::forget('dashboard_stats');
        Cache::forget('dashboard_chart');

        return back()->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikoreksi.');
    }

    public function masuk(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'masuk');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('transaksi.masuk', compact('transaksis'));
    }

    public function keluar(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])
            ->whereIn('jenis_transaksi', ['keluar', 'retur_customer', 'retur_produksi']);
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('transaksi.keluar', compact('transaksis'));
    }

    public function retur(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_customer');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();
        return view('retur.index', compact('transaksis'));
    }

    public function returProduksi(Request $request)
    {
        $query = Transaksi::with(['barang', 'user'])->where('jenis_transaksi', 'retur_produksi');
        $this->applyFilters($query, $request);
        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(20)->withQueryString();
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
                $q->whereHas('barang', fn($q2) => $q2->where('nama_barang', 'like', "%{$search}%"))
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
