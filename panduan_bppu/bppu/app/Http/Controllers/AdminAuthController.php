<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ActivityLogger;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan halaman login admin
     */
    public function showLoginForm()
    {
        return Inertia::render('Admin/Login');
    }

    /**
     * Proses login admin
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // SECURITY: Validasi bahwa user memiliki role admin yang valid
            $allowedRoles = ['officer', 'sysadmin', 'head', 'canteen', 'shop', 'data_clerk'];
            if (!$user->hasAnyRole($allowedRoles)) {
                Auth::logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'login' => 'Akses ditolak. Anda tidak memiliki hak akses sebagai pengelola.',
                ])->onlyInput('login');
            }

            ActivityLogger::login($user);

            // Redirect kantin user langsung ke PoS
            if ($user->hasRole('canteen')) {
                return redirect()->intended('/pengelola/penjualan');
            }

            // Redirect data_clerk user langsung ke Arsip
            if ($user->hasRole('data_clerk')) {
                return redirect()->intended('/pengelola/arsip');
            }

            return redirect()->intended('/pengelola/dasbor');
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    /**
     * Update password pertama kali
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        $user->password = bcrypt($request->new_password);
        $user->must_change_password = false;
        $user->save();

        // Redirect kantin user ke PoS setelah ganti password
        if ($user->hasRole('canteen')) {
            return redirect('/pengelola/penjualan')->with('success', 'Password berhasil diubah!');
        }

        // Redirect data_clerk user ke Arsip setelah ganti password
        if ($user->hasRole('data_clerk')) {
            return redirect('/pengelola/arsip')->with('success', 'Password berhasil diubah!');
        }

        return redirect('/pengelola/dasbor')->with('success', 'Password berhasil diubah!');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::logout($user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/pengelola/login');
    }
}
