<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('username')->paginate(20);
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
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'max:255', 'confirmed'],
            'role'     => 'required|in:admin,kepala_gudang,operator',
        ]);

        $phone = $request->phone ? ('62' . ltrim(preg_replace('/[^0-9]/', '', $request->phone), '62')) : null;

        $user = User::create([
            'username'   => $request->username,
            'email'      => $request->email ?: null,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => $request->boolean('is_active', true),
            'phone'      => $phone,
            'wa_api_key' => $request->wa_api_key ?: null,
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
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers(), 'max:255', 'confirmed'],
        ]);

        $phone = $request->phone ? ('62' . ltrim(preg_replace('/[^0-9]/', '', $request->phone), '62')) : null;

        $isActive = $request->boolean('is_active');
        if ($user->id === auth()->id() && !$isActive) {
            return back()->withInput()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $data = [
            'username'   => $request->username,
            'email'      => $request->email ?: null,
            'role'       => $request->role,
            'is_active'  => $isActive,
            'phone'      => $phone,
            'wa_api_key' => $request->wa_api_key ?: null,
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
        if ($user->id === auth()->id()) {
            return redirect()->route('user.index')->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }
        $user->update(['is_active' => false]);
        AuditLog::log('delete', "Nonaktifkan user: {$user->username}", $user, ['is_active' => true], ['is_active' => false]);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dinonaktifkan');
    }
}
