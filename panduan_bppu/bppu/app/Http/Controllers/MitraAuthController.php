<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MitraAuthController extends Controller
{
    private const ALLOWED_ROLES = ['supplier', 'tenant', 'vendor', 'distributor', 'produsen', 'reseller', 'konsinyasi'];

    public function showLoginForm()
    {
        return Inertia::render('Mitra/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password'  => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->hasAnyRole(self::ALLOWED_ROLES)) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'login' => 'Akun ini tidak memiliki akses sebagai mitra.',
                ])->onlyInput('login');
            }

            return redirect()->intended('/mitra/dasbor');
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/mitra/login')->with('success', 'Anda berhasil keluar.');
    }
}
