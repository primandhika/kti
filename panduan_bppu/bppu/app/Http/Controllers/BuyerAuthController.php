<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BuyerAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Pastikan hanya buyer yang bisa login via route ini
            if (!$user->hasRole('buyer')) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun ini tidak memiliki akses sebagai pembeli.',
                ]);
            }

            // Gunakan redirect_to dari request jika ada (internal URL saja)
            $redirectTo = $request->input('redirect_to');
            $fallback = ($redirectTo && str_starts_with($redirectTo, '/'))
                ? $redirectTo
                : route('self-order.index');

            return redirect()->intended($fallback)
                ->with('success', 'Login berhasil! Selamat datang, ' . $user->name . '.');
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'member_since' => now(),
            'total_points' => 0,
        ]);

        // Assign buyer role
        $user->assignRole('buyer');

        // Generate member code
        $memberCode = 'MBR-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
        $user->update(['member_code' => $memberCode]);

        // Auto login after registration
        Auth::login($user);

        return redirect(route('self-order.index'))->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda berhasil keluar dari akun.');
    }
}
