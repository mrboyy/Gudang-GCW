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
                  ->orWhere('kode_barang', 'like', "%{$request->search}%");
            });
        }
        $barangs = $query->orderBy('nama_barang')->paginate(20)->withQueryString();
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
            'satuan'       => 'required|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0|max:999999',
            'deskripsi'    => 'nullable|string|max:1000',
            'fotoFile'     => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ], [
            'kode_barang.required' => 'Kode barang wajib diisi',
            'kode_barang.unique'   => 'Kode barang sudah digunakan',
            'nama_barang.required' => 'Nama barang wajib diisi',
        ]);

        $data = $request->only(['kode_barang', 'nama_barang', 'merk', 'satuan', 'deskripsi']);
        $data['stok_minimum'] = $request->stok_minimum ?? 0;

        if ($request->filled('foto_base64') && str_starts_with($request->foto_base64, 'data:image')) {
            $b64     = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto_base64);
            $decoded = base64_decode($b64, true);
            // Tolak SVG dan non-image — cek magic bytes
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($decoded);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                return back()->withInput()->withErrors(['fotoFile' => 'Format foto tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.']);
            }
            $ext  = explode('/', $mime)[1];
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($name, $decoded);
            $data['foto'] = $name;
        } elseif ($request->hasFile('fotoFile')) {
            $ext  = $request->file('fotoFile')->getClientOriginalExtension();
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            $request->file('fotoFile')->storeAs('', $name, 'public');
            $data['foto'] = $name;
        }

        $barang = Barang::create($data);
        AuditLog::log('create', "Tambah barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang, [], [
            'nama_barang'  => $barang->nama_barang,
            'kode_barang'  => $barang->kode_barang,
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
            'satuan'       => 'required|string|max:50',
            'stok_minimum' => 'nullable|integer|min:0|max:999999',
            'deskripsi'    => 'nullable|string|max:1000',
            'fotoFile'     => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:2048',
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

        if ($request->filled('foto_base64') && str_starts_with($request->foto_base64, 'data:image')) {
            $b64     = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto_base64);
            $decoded = base64_decode($b64, true);
            $mime    = (new \finfo(FILEINFO_MIME_TYPE))->buffer($decoded);
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                return back()->withInput()->withErrors(['fotoFile' => 'Format foto tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.']);
            }
            if ($barang->foto) Storage::disk('public')->delete($barang->foto);
            $ext  = explode('/', $mime)[1];
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($name, $decoded);
            $data['foto'] = $name;
        } elseif ($request->hasFile('fotoFile')) {
            if ($barang->foto) Storage::disk('public')->delete($barang->foto);
            $ext  = $request->file('fotoFile')->getClientOriginalExtension();
            $name = 'barang/' . Str::uuid() . '.' . $ext;
            $request->file('fotoFile')->storeAs('', $name, 'public');
            $data['foto'] = $name;
        }

        $old = $barang->only(['nama_barang','kode_barang','satuan','stok_minimum','is_active']);
        $barang->update($data);
        AuditLog::log('update', "Ubah barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang,
            $old,
            $barang->only(['nama_barang','kode_barang','satuan','stok_minimum','is_active'])
        );
        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        if (\App\Models\Transaksi::where('id_barang', $barang->id)->exists()) {
            return redirect()->route('barang.index')
                ->with('error', "Barang \"{$barang->nama_barang}\" tidak bisa dihapus karena memiliki riwayat transaksi.");
        }
        if ($barang->foto) Storage::disk('public')->delete($barang->foto);
        AuditLog::log('delete', "Hapus barang: {$barang->nama_barang} ({$barang->kode_barang})", null,
            ['nama_barang' => $barang->nama_barang, 'kode_barang' => $barang->kode_barang], []
        );
        $nama = $barang->nama_barang;
        $barang->delete();
        return redirect()->route('barang.index')->with('success', "Barang \"{$nama}\" berhasil dihapus.");
    }

    public function toggleActive(Barang $barang)
    {
        $newState = !$barang->is_active;
        $barang->update(['is_active' => $newState]);
        AuditLog::log('update', ($newState ? 'Aktifkan' : 'Nonaktifkan') . " barang: {$barang->nama_barang} ({$barang->kode_barang})", $barang,
            ['is_active' => !$newState], ['is_active' => $newState]
        );
        $msg = $newState ? 'Barang berhasil diaktifkan' : 'Barang berhasil dinonaktifkan';
        return redirect()->route('barang.index')->with('success', $msg);
    }
}
