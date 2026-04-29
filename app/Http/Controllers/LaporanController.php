<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function stok(Request $request)
    {
        // Hanya load lot yang stoknya > 0
        $query = Barang::with(['stoks' => fn($q) => $q->where('stok_akhir', '>', 0)->orderBy('nomor_lot')])
                       ->where('is_active', true)
                       ->whereHas('stoks', fn($q) => $q->where('stok_akhir', '>', 0));

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }

        if ($request->merk) {
            $query->where('merk', $request->merk);
        }

        $groupBy = $request->group_by;
        match ($groupBy) {
            'merk'   => $query->orderBy('merk')->orderBy('nama_barang'),
            'satuan' => $query->orderBy('satuan')->orderBy('nama_barang'),
            default  => $query->orderBy('nama_barang'),
        };

        $barangs = $query->paginate(20)->withQueryString();
        $merks   = Barang::where('is_active', true)->whereNotNull('merk')
                         ->distinct()->orderBy('merk')->pluck('merk');

        return view('laporan.stok', compact('barangs', 'merks', 'groupBy'));
    }

    public function exportStok(Request $request)
    {
        $query = Barang::with(['stoks' => fn($q) => $q->where('stok_akhir', '>', 0)->orderBy('nomor_lot')])
                       ->where('is_active', true)
                       ->whereHas('stoks', fn($q) => $q->where('stok_akhir', '>', 0));
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }
        if ($request->merk) $query->where('merk', $request->merk);

        match ($request->group_by) {
            'merk'   => $query->orderBy('merk')->orderBy('nama_barang'),
            'satuan' => $query->orderBy('satuan')->orderBy('nama_barang'),
            default  => $query->orderBy('nama_barang'),
        };

        $barangs  = $query->get();
        $filename = 'laporan-stok-' . date('Y-m-d') . '.csv';
        $sep      = ';'; // Excel Indonesia pakai ; sebagai pemisah

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($barangs, $sep) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($file, ['Nama Barang', 'Merk', 'Kode', 'Satuan', 'Nomor Lot', 'Stok'], $sep);

            foreach ($barangs as $b) {
                $totalStok = $b->stoks->sum('stok_akhir');

                // Baris barang — total
                fputcsv($file, [
                    $b->nama_barang,
                    $b->merk ?? '-',
                    $b->kode_barang,
                    $b->satuan,
                    '(Total)',
                    $totalStok,
                ], $sep);

                // Baris tiap lot
                foreach ($b->stoks as $s) {
                    fputcsv($file, [
                        '    ' . ($s->nomor_lot ? 'Lot: ' . $s->nomor_lot : 'Tanpa Lot'),
                        '',
                        '',
                        $b->satuan,
                        $s->nomor_lot ?? '-',
                        $s->stok_akhir,
                    ], $sep);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function transaksi(Request $request)
    {
        if ($request->periode === 'hari') {
            $tanggalDari   = today()->format('Y-m-d');
            $tanggalSampai = $tanggalDari;
        } elseif ($request->periode === 'bulan') {
            $tanggalDari   = Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggalSampai = Carbon::now()->format('Y-m-d');
        } elseif ($request->periode === 'tahun') {
            $tanggalDari   = Carbon::now()->startOfYear()->format('Y-m-d');
            $tanggalSampai = Carbon::now()->format('Y-m-d');
        } else {
            $tanggalDari   = $request->tanggal_dari ?? Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');
        }

        $query = Transaksi::with(['barang', 'user'])
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($request->jenis_transaksi) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }
        if ($request->merk) {
            $query->whereHas('barang', fn($q) => $q->where('merk', 'like', "%{$request->merk}%"));
        }
        if ($request->nomor_lot) {
            $query->where('nomor_lot', 'like', "%{$request->nomor_lot}%");
        }

        $transaksis = $query->orderByDesc('tanggal')->orderByDesc('id')->get();
        $merks = Barang::where('is_active', true)->distinct()->pluck('merk');

        return view('laporan.transaksi', compact('transaksis', 'tanggalDari', 'tanggalSampai', 'merks'));
    }

    public function exportExcel(Request $request)
    {
        $tanggalDari   = $request->tanggal_dari ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');

        $query = Transaksi::with(['barang', 'user'])
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($request->jenis_transaksi) $query->where('jenis_transaksi', $request->jenis_transaksi);
        if ($request->merk) $query->whereHas('barang', fn($q) => $q->where('merk', 'like', "%{$request->merk}%"));
        if ($request->nomor_lot) $query->where('nomor_lot', 'like', "%{$request->nomor_lot}%");

        $transaksis = $query->orderByDesc('tanggal')->get();

        // Simpan log laporan
        Laporan::create([
            'id_user'         => Auth::id(),
            'tanggal_generate'=> today(),
            'jenis_laporan'   => 'Transaksi ' . $tanggalDari . ' s/d ' . $tanggalSampai,
            'file_path'       => null,
        ]);

        // Generate CSV (tanpa library tambahan)
        $filename = 'laporan-transaksi-' . $tanggalDari . '-sd-' . $tanggalSampai . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transaksis) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            $sep = ';';
            fputcsv($file, ['No. Transaksi', 'Jenis', 'Tanggal', 'Nama Barang', 'Merk', 'Quantity', 'Satuan', 'No. Lot', 'Operator', 'Keterangan'], $sep);
            foreach ($transaksis as $t) {
                fputcsv($file, [
                    $t->no_transaksi,
                    strtoupper($t->jenis_transaksi),
                    $t->tanggal->format('d/m/Y'),
                    $t->barang->nama_barang,
                    $t->barang->merk,
                    $t->quantity,
                    $t->barang->satuan,
                    $t->nomor_lot ?? '-',
                    $t->user->username,
                    $t->keterangan ?? '',
                ], $sep);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
