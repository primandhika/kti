<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LokasiMeja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class MemberAreaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('buyer')) {
            return redirect('/kantin/self-order');
        }

        $lokasiMejas = LokasiMeja::where('is_active', true)
            ->with(['mejas' => function ($q) {
                $q->where('is_active', true)->orderBy('display_order')->orderBy('nomor');
            }])
            ->orderBy('display_order')
            ->get()
            ->map(fn($l) => [
                'id'    => $l->id,
                'nama'  => $l->nama,
                'kode'  => $l->kode,
                'mejas' => $l->mejas->map(fn($m) => [
                    'id'        => $m->id,
                    'kode_meja' => $m->kode_meja,
                    'nama'      => $m->nama,
                    'nomor'     => $m->nomor,
                ]),
            ]);

        $defaultMeja = null;
        if ($user->default_meja_id) {
            $user->load('defaultMeja.lokasi');
            $dm = $user->defaultMeja;
            if ($dm && $dm->is_active) {
                $defaultMeja = [
                    'id'        => $dm->id,
                    'kode_meja' => $dm->kode_meja,
                    'nama'      => $dm->nama,
                    'nomor'     => $dm->nomor,
                    'lokasi'    => $dm->lokasi?->nama,
                ];
            }
        }

        return Inertia::render('SelfOrder/MemberArea', [
            'user' => [
                'name'         => $user->name,
                'email'        => $user->email,
                'username'     => $user->username,
                'phone'        => $user->phone,
                'member_code'  => $user->member_code,
                'total_points' => $user->total_points ?? 0,
                'member_since' => $user->member_since?->format('d/m/Y'),
            ],
            'defaultMeja'  => $defaultMeja,
            'lokasiMejas'  => $lokasiMejas,
        ]);
    }

    public function updateDefaultMeja(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('buyer')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'meja_id' => 'nullable|integer|exists:mejas,id',
        ]);

        $user->update(['default_meja_id' => $request->meja_id]);

        $label = null;
        if ($request->meja_id) {
            $user->load('defaultMeja.lokasi');
            $dm = $user->defaultMeja;
            if ($dm) {
                $parts = [];
                if ($dm->lokasi?->nama) $parts[] = $dm->lokasi->nama;
                $parts[] = $dm->nama ?: $dm->kode_meja;
                $label = implode(' - ', $parts);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $request->meja_id ? 'Meja default berhasil disimpan.' : 'Meja default berhasil dihapus.',
            'label'   => $label,
        ]);
    }

    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('buyer')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('buyer')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors'  => ['current_password' => ['Password saat ini tidak sesuai.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
