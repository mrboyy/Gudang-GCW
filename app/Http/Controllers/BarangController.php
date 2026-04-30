<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $barangs = $query->orderBy('nama_barang')->paginate(24)->withQueryString();
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'  => 'required|string|max:100|unique:barangs,kode_barang',
            'nama_barang'  => 'required|string|max:255',
            'merk'         => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0|max:999999',
            'deskripsi'    => 'nullable|string|max:1000',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi',
            'kode_barang.unique'   => 'Kode barang sudah digunakan',
            'nama_barang.required' => 'Nama barang wajib diisi',
            'merk.required'        => 'Merk wajib diisi',
            'foto.image'           => 'File harus berupa gambar',
            'foto.mimes'           => 'Format foto harus jpg, png, atau webp',
            'foto.max'             => 'Ukuran foto maksimal 2MB',
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'merk', 'satuan', 'stok_minimum', 'deskripsi']);

        if ($request->hasFile('foto')) {
            $ext  = $request->file('foto')->getClientOriginalExtension();
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            $request->file('foto')->storeAs('', $name, 'public');
            $data['foto'] = $name;
        }

        $barang = Barang::create($data);
        AuditLog::log('create', "Tambah barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang, [], [
            'nama_barang'  => $barang->nama_barang,
            'kode_barang'  => $barang->kode_barang,
            'merk'         => $barang->merk,
            'satuan'       => $barang->satuan,
            'stok_minimum' => $barang->stok_minimum,
        ]);
        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang'  => 'required|string|max:100|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang'  => 'required|string|max:255',
            'merk'         => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'stok_minimum' => 'required|integer|min:0|max:999999',
            'deskripsi'    => 'nullable|string|max:1000',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'foto.image'  => 'File harus berupa gambar',
            'foto.mimes'  => 'Format foto harus jpg, png, atau webp',
            'foto.max'    => 'Ukuran foto maksimal 2MB',
        ]);

        $data = [
            'kode_barang'  => $request->kode_barang,
            'nama_barang'  => $request->nama_barang,
            'merk'         => $request->merk,
            'satuan'       => $request->satuan,
            'stok_minimum' => $request->stok_minimum ?? 0,
            'deskripsi'    => $request->deskripsi,
            'is_active'    => $request->boolean('is_active'),
        ];

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $ext  = $request->file('foto')->getClientOriginalExtension();
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            $request->file('foto')->storeAs('', $name, 'public');
            $data['foto'] = $name;
        }

        $old = $barang->only(['nama_barang','kode_barang','merk','satuan','stok_minimum','is_active']);
        $barang->update($data);
        AuditLog::log('update', "Ubah barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang,
            $old,
            $barang->only(['nama_barang','kode_barang','merk','satuan','stok_minimum','is_active'])
        );
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->update(['is_active' => false]);
        AuditLog::log('delete', "Nonaktifkan barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang,
            ['is_active' => true], ['is_active' => false]
        );
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dinonaktifkan');
    }
}
