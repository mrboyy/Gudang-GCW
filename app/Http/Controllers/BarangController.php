<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_barang', 'like', "%{$request->search}%")
                  ->orWhere('kode_barang', 'like', "%{$request->search}%")
                  ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }
        $barangs = $query->orderBy('nama_barang')->paginate(15)->withQueryString();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'merk' => 'required',
            'satuan' => 'required',
            'stok_minimum' => 'required|integer|min:0',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi',
            'kode_barang.unique' => 'Kode barang sudah digunakan',
            'nama_barang.required' => 'Nama barang wajib diisi',
            'merk.required' => 'Merk wajib diisi',
        ]);

        Barang::create($request->all());
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required',
            'merk' => 'required',
            'satuan' => 'required',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $barang->update([
            'kode_barang'  => $request->kode_barang,
            'nama_barang'  => $request->nama_barang,
            'merk'         => $request->merk,
            'satuan'       => $request->satuan,
            'stok_minimum' => $request->stok_minimum ?? 0,
            'deskripsi'    => $request->deskripsi,
            'is_active'    => $request->boolean('is_active'),
        ]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->update(['is_active' => false]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dinonaktifkan');
    }
}
