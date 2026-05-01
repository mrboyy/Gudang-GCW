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
            ->whereHas('stoks', fn($q) => $q->where('stok_akhir', '>', 0));

        if ($filters['search'] ?? null) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        if ($filters['merk'] ?? null) {
            $query->where('merk', $filters['merk']);
        }

        match ($filters['group_by'] ?? null) {
            'merk'   => $query->orderBy('merk')->orderBy('nama_barang'),
            'satuan' => $query->orderBy('satuan')->orderBy('nama_barang'),
            default  => $query->orderBy('nama_barang'),
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

        if ($filters['merk'] ?? null) {
            $merk = $filters['merk'];
            $query->whereHas('barang', fn($q) => $q->where('merk', 'like', "%{$merk}%"));
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
