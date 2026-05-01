<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            'email'    => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', 'confirmed'],
            'role'     => 'required|in:admin,kepala_gudang,operator',
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.min'   => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'username'  => $request->username,
            'email'     => $request->email ?: null,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active', true),
        ]);
        AuditLog::log('create', "Tambah user: {$user->username} ({$user->role})", $user, [], [
            'username' => $user->username,
            'role'     => $user->role,
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
            'email'    => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => 'required|in:admin,kepala_gudang,operator',
            'password' => ['nullable', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', 'confirmed'],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.min'   => 'Password minimal 8 karakter.',
        ]);

        $data = [
            'username'  => $request->username,
            'email'     => $request->email ?: null,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $old = $user->only(['username','role','is_active']);
        $user->update($data);
        AuditLog::log('update', "Ubah user: {$user->username}", $user, $old, $user->only(['username','role','is_active']));
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        $user->update(['is_active' => false]);
        AuditLog::log('delete', "Nonaktifkan user: {$user->username}", $user, ['is_active' => true], ['is_active' => false]);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dinonaktifkan');
    }
}
