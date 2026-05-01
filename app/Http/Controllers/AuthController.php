<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi administrator.']);
            }
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        \App\Models\AuditLog::create([
            'user_id'     => null,
            'action'      => 'failed_login',
            'model_type'  => 'User',
            'model_id'    => null,
            'description' => 'Percobaan login gagal untuk username: ' . $request->username,
            'ip_address'  => $request->ip(),
            'old_values'  => null,
            'new_values'  => json_encode(['username' => $request->username]),
        ]);

        return back()->withErrors(['username' => 'Username atau password salah. Periksa kembali dan coba lagi.'])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
