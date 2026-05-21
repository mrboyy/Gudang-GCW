<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class LaporanService
{
    /**
     * Buat query dasar laporan stok dengan eager load dan filter.
     * Kembalikan Builder agar controller bisa menambahkan paginate() atau get().
     */
    public function buildStokQuery(array $filters = []): Builder
    {
        $query = Barang::with(['stoks' => fn($q) => $q->where('stok_akhir', '>', 0)->orderBy('nomor_lot')])
            ->where('is_active', true)
            ->withSum('stoks as stok_total', 'stok_akhir');

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where('nama_barang', 'like', "%{$search}%");
        }

        // Filter berdasarkan tanggal update stok
        $mode = $filters['update_mode'] ?? null;
        if ($mode === 'hari' && ($filters['update_tanggal'] ?? null)) {
            $tgl = $filters['update_tanggal'];
            $query->whereRaw(
                '(SELECT MAX(DATE(tanggal_update)) FROM stoks WHERE id_barang = barangs.id) = ?',
                [$tgl]
            );
        } elseif ($mode === 'bulan' && ($filters['update_bulan'] ?? null) && ($filters['update_tahun'] ?? null)) {
            $bln = $filters['update_bulan'];
            $thn = $filters['update_tahun'];
            $query->whereRaw(
                "DATE_FORMAT((SELECT MAX(tanggal_update) FROM stoks WHERE id_barang = barangs.id), '%Y-%m') = ?",
                [$thn . '-' . str_pad($bln, 2, '0', STR_PAD_LEFT)]
            );
        } elseif ($mode === 'tahun' && ($filters['update_tahun'] ?? null)) {
            $thn = $filters['update_tahun'];
            $query->whereRaw(
                'YEAR((SELECT MAX(tanggal_update) FROM stoks WHERE id_barang = barangs.id)) = ?',
                [$thn]
            );
        }

        match ($filters['group_by'] ?? null) {
            'satuan'  => $query->orderBy('satuan')->orderBy('nama_barang'),
            'merk'    => $query->orderByRaw('COALESCE(merk, "") ASC')->orderBy('nama_barang'),
            'tanggal' => $query
                ->withMax('stoks as last_update', 'tanggal_update')
                ->orderByRaw('(SELECT MAX(tanggal_update) FROM stoks WHERE id_barang = barangs.id) DESC')
                ->orderBy('nama_barang'),
            default   => $query->orderBy('nama_barang'),
        };

        return $query;
    }

    /**
     * Buat query dasar laporan transaksi dengan eager load dan filter.
     * Kembalikan Builder agar controller bisa menambahkan get() atau orderBy().
     */
    public function buildTransaksiQuery(array $filters = []): Builder
    {
        $tanggalDari   = $filters['tanggal_dari']   ?? now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $filters['tanggal_sampai'] ?? now()->format('Y-m-d');

        $query = Transaksi::with(['barang', 'user'])
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($filters['jenis_transaksi'] ?? null) {
            $query->where('jenis_transaksi', $filters['jenis_transaksi']);
        }

        if ($filters['nomor_lot'] ?? null) {
            $query->where('nomor_lot', 'like', "%{$filters['nomor_lot']}%");
        }

        return $query;
    }

    /**
     * Ambil data transaksi lengkap (untuk view & export).
     */
    public function getDataTransaksi(array $filters = []): EloquentCollection
    {
        return $this->buildTransaksiQuery($filters)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }
}
