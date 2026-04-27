<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('username')->paginate(15);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:admin,kepala_gudang,operator',
        ]);

        User::create([
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|unique:users,username,' . $user->id,
            'role'     => 'required|in:admin,kepala_gudang,operator',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'username'  => $request->username,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => false]);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dinonaktifkan');
    }
}
