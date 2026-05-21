<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    protected int $timeoutMinutes = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $lastActivity = session('last_activity_at');

        if ($lastActivity && (time() - $lastActivity) > ($this->timeoutMinutes * 60)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with(
                'error',
                'Sesi Anda telah berakhir karena tidak aktif selama ' . $this->timeoutMinutes . ' menit. Silakan login kembali.'
            );
        }

        session(['last_activity_at' => time()]);

        return $next($request);
    }
}
