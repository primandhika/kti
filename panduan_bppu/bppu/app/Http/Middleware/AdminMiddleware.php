<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest('/pengelola/login');
        }

        $user = Auth::user();

        // SECURITY: Hanya izinkan user dengan role admin yang valid
        $allowedRoles = ['officer', 'sysadmin', 'head', 'canteen', 'shop', 'data_clerk'];
        if (!$user->hasAnyRole($allowedRoles)) {
            Auth::logout();
            return redirect('/pengelola/login')->with('error', 'Akses tidak diizinkan. Anda tidak memiliki hak akses sebagai pengelola.');
        }

        // Redirect kantin & shop user yang coba akses dashboard ke PoS
        if ($user->hasAnyRole(['canteen', 'shop']) &&
            !$user->hasAnyRole(['officer', 'sysadmin']) &&
            $request->is('pengelola/dasbor')) {
            return redirect('/pengelola/penjualan');
        }

        // Redirect data_clerk yang coba akses selain path yang diizinkan
        if ($user->hasRole('data_clerk') &&
            !$request->is('pengelola/arsip*') &&
            !$request->is('pengelola/opname-stock*') &&
            !$request->is('pengelola/menu-kantin*') &&
            !$request->is('pengelola/belanja*') &&
            !$request->is('pengelola/manajemen-pengguna*') &&
            !$request->is('pengelola/profil*') &&
            !$request->is('pengelola/logout') &&
            !$request->is('pengelola/update-password')) {
            return redirect('/pengelola/arsip');
        }

        return $next($request);
    }
}
