<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Laporan;
use App\Services\LaporanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(private LaporanService $laporanService) {}

    public function stok(Request $request)
    {
        $filters = $request->only(['search', 'group_by', 'update_mode', 'update_tanggal', 'update_bulan', 'update_tahun']);
        $groupBy = $request->group_by;

        $barangs = $this->laporanService->buildStokQuery($filters)
                        ->paginate(25)
                        ->withQueryString();

        return view('laporan.stok', compact('barangs', 'groupBy'));
    }

    public function exportStok(Request $request)
    {
        $filters  = $request->only(['search', 'group_by']);
        $barangs  = $this->laporanService->buildStokQuery($filters)->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'Nama Produk / Lot');
        $sheet->setCellValue('B1', 'Quantity Per Lot');
        $sheet->setCellValue('C1', 'Total Stok');
        
        // Bold header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        
        $row = 2;
        foreach ($barangs as $b) {
            $totalStok = $b->stoks->sum('stok_akhir');
            
            // Baris produk utama
            $sheet->setCellValue('A' . $row, $b->nama_barang);
            $sheet->setCellValue('C' . $row, $totalStok);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;
            
            // Detail lot
            foreach ($b->stoks as $s) {
                $sheet->setCellValue('A' . $row, '   Lot: ' . ($s->nomor_lot ?? 'Tanpa Lot'));
                $sheet->setCellValue('B' . $row, $s->stok_akhir);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row++;
            }
        }
        
        // Auto-width for column A
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        
        Laporan::create([
            'id_user'          => Auth::id(),
            'tanggal_generate' => today(),
            'jenis_laporan'    => 'Export Excel Stok',
            'file_path'        => null,
        ]);

        $filename = 'laporan-stok-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function printStok(Request $request)
    {
        $filters = $request->only(['search']);
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
            $tanggalDari   = $request->tanggal_dari   ?? '2020-01-01';
            $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');
        }

        $filters = [
            'tanggal_dari'    => $tanggalDari,
            'tanggal_sampai'  => $tanggalSampai,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nomor_lot'       => $request->nomor_lot,
        ];

        $transaksis = $this->laporanService->getDataTransaksi($filters);

        return view('laporan.transaksi', compact('transaksis', 'tanggalDari', 'tanggalSampai'));
    }

    public function exportExcel(Request $request)
    {
        $tanggalDari   = $request->tanggal_dari   ?? '2020-01-01';
        $tanggalSampai = $request->tanggal_sampai ?? Carbon::now()->format('Y-m-d');

        $filters = [
            'tanggal_dari'    => $tanggalDari,
            'tanggal_sampai'  => $tanggalSampai,
            'jenis_transaksi' => $request->jenis_transaksi,
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $headers = ['No. Transaksi', 'No. Ref', 'Jenis', 'Tanggal', 'Nama Barang (Lot)', 'Quantity', 'Satuan', 'Supplier', 'Tujuan', 'Operator', 'Keterangan'];
        $column = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($column . '1', $h);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $sheet->getStyle('A1:' . --$column . '1')->getFont()->setBold(true);

        $row = 2;
        foreach ($transaksis as $t) {
            $sheet->setCellValue('A' . $row, $t->no_transaksi);
            $sheet->setCellValue('B' . $row, $t->no_ref ?? '-');
            $sheet->setCellValue('C' . $row, strtoupper($t->jenis_transaksi));
            $sheet->setCellValue('D' . $row, $t->tanggal->format('d/m/Y'));
            $sheet->setCellValue('E' . $row, $t->barang->nama_barang . ($t->nomor_lot ? " (Lot: {$t->nomor_lot})" : ""));
            $sheet->setCellValue('F' . $row, $t->quantity);
            $sheet->setCellValue('G' . $row, $t->barang->satuan);
            $sheet->setCellValue('H' . $row, $t->nama_supplier ?? '-');
            $sheet->setCellValue('I' . $row, $t->tujuan_keluar ?? '-');
            $sheet->setCellValue('J' . $row, $t->user->username);
            $sheet->setCellValue('K' . $row, $t->keterangan ?? '');
            $row++;
        }

        $filename = 'laporan-transaksi-' . $tanggalDari . '-sd-' . $tanggalSampai . '.xlsx';
        
        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
