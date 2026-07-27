<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MitraMiddleware
{
    private const ALLOWED_ROLES = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/mitra/login');
        }

        $user = Auth::user();

        if (!$user->hasAnyRole(self::ALLOWED_ROLES)) {
            Auth::logout();
            return redirect('/mitra/login')->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
