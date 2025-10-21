<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionExpired
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Session::has('last_activity')) {
                // Cek apakah user sudah tidak aktif lebih dari waktu session
                if (time() - Session::get('last_activity') > config('session.lifetime') * 60) {
                    Auth::logout();
                    Session::flush(); // Hapus semua session
                    return redirect()->route('login')->with('message', 'Sesi Anda telah habis. Silakan login kembali.');
                }
            }

            // Perbarui waktu aktivitas terakhir
            Session::put('last_activity', time());
        }

        return $next($request);
    }
}
