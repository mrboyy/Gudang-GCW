<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = Supplier::orderBy('nama');
        if ($request->filled('search')) {
            $q->where('nama', 'like', '%' . $request->search . '%');
        }
        $suppliers = $q->paginate(20)->withQueryString();
        return view('supplier.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:200|unique:suppliers,nama']);

        $supplier = Supplier::create(['nama' => trim($request->nama)]);
        AuditLog::log('create', "Tambah supplier: {$supplier->nama}", $supplier, [], ['nama' => $supplier->nama]);

        return redirect()->route('supplier.index')->with('success', "Supplier \"{$supplier->nama}\" berhasil ditambahkan.");
    }

    public function destroy(Supplier $supplier)
    {
        AuditLog::log('delete', "Hapus supplier: {$supplier->nama}", $supplier, ['nama' => $supplier->nama], []);
        $supplier->delete();
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
