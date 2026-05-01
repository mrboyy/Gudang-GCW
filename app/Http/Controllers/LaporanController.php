<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Laporan;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $laporanService) {}

    public function stok(Request $request)
    {
        $filters = $request->only(['search', 'merk', 'group_by']);
        $groupBy = $request->group_by;

        $barangs = $this->laporanService->buildStokQuery($filters)
                        ->paginate(20)
                        ->withQueryString();

        $merks = Barang::where('is_active', true)->whereNotNull('merk')
                       ->distinct()->orderBy('merk')->pluck('merk');

        return view('laporan.stok', compact('barangs', 'merks', 'groupBy'));
    }

    public function exportStok(Request $request)
    {
        $filters  = $request->only(['search', 'merk', 'group_by']);
        $barangs  = $this->laporanService->buildStokQuery($filters)->get();
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

    public function printStok(Request $request)
    {
        $filters = $request->only(['search', 'merk']);
        // printStok selalu order by nama_barang tanpa group_by
        $barangs = $this->laporanService->buildStokQuery($filters)->get();
        return view('laporan.print-stok', compact('barangs'));
    }

    public function printTransaksi(Request $request)
    {
        $tanggalDari   = $request->tanggal_dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');

        $filters = [
            'tanggal_dari'    => $tanggalDari,
            'tanggal_sampai'  => $tanggalSampai,
            'jenis_transaksi' => $request->jenis_transaksi,
            'merk'            => $request->merk,
        ];

        $transaksis = $this->laporanService->getDataTransaksi($filters);
        return view('laporan.print-transaksi', compact('transaksis', 'tanggalDari', 'tanggalSampai'));
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
            $tanggalDari   = $request->tanggal_dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');
        }

        $filters = [
            'tanggal_dari'    => $tanggalDari,
            'tanggal_sampai'  => $tanggalSampai,
            'jenis_transaksi' => $request->jenis_transaksi,
            'merk'            => $request->merk,
            'nomor_lot'       => $request->nomor_lot,
        ];

        $transaksis = $this->laporanService->getDataTransaksi($filters);
        $merks      = Barang::where('is_active', true)->distinct()->pluck('merk');

        return view('laporan.transaksi', compact('transaksis', 'tanggalDari', 'tanggalSampai', 'merks'));
    }

    public function exportExcel(Request $request)
    {
        $tanggalDari   = $request->tanggal_dari   ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');

        $filters = [
            'tanggal_dari'    => $tanggalDari,
            'tanggal_sampai'  => $tanggalSampai,
            'jenis_transaksi' => $request->jenis_transaksi,
            'merk'            => $request->merk,
            'nomor_lot'       => $request->nomor_lot,
        ];

        $transaksis = $this->laporanService->getDataTransaksi($filters);

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
