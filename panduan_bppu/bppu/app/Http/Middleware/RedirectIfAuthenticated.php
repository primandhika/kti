<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // SECURITY: Hanya redirect ke dashboard jika user punya role admin
                $allowedRoles = ['officer', 'sysadmin', 'head', 'canteen', 'shop'];
                if ($user->hasAnyRole($allowedRoles)) {
                    // Redirect kantin user ke PoS
                    if ($user->hasRole('canteen') && !$user->hasAnyRole(['officer', 'sysadmin'])) {
                        return redirect('/pengelola/penjualan');
                    }
                    return redirect('/pengelola/dasbor');
                }

                // Mitra redirect ke portal mitra
                $mitraRoles = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];
                if ($user->hasAnyRole($mitraRoles)) {
                    return redirect('/mitra/dasbor');
                }

                // Buyer atau role lain redirect ke home
                return redirect('/');
            }
        }

        return $next($request);
    }
}
