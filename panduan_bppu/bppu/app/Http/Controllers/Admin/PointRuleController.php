<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointRule;
use Illuminate\Http\Request;

class PointRuleController extends Controller
{
    public function index()
    {
        $rules = PointRule::orderBy('order')->get();
        return response()->json($rules);
    }

    private function validateAndNormalizeRule(Request $request): array
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'min_amount'  => 'required|integer|min:0',
            'max_amount'  => 'nullable|integer|min:0',
            'points'      => 'required|integer|min:0',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['max_amount'] = ($validated['max_amount'] ?? null) ?: null;

        if ($validated['max_amount'] !== null && $validated['max_amount'] <= $validated['min_amount']) {
            abort(422, 'Maks. transaksi harus lebih besar dari min. transaksi');
        }

        return $validated;
    }

    public function store(Request $request)
    {
        $validated = $this->validateAndNormalizeRule($request);
        $validated['order'] = (PointRule::max('order') ?? 0) + 1;
        PointRule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rule poin berhasil ditambahkan',
            'data'    => PointRule::orderBy('order')->get(),
        ]);
    }

    public function update(Request $request, PointRule $pointRule)
    {
        $validated = $this->validateAndNormalizeRule($request);
        $pointRule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rule poin berhasil diperbarui',
            'data'    => PointRule::orderBy('order')->get(),
        ]);
    }

    public function destroy(PointRule $pointRule)
    {
        $pointRule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rule poin berhasil dihapus',
            'data'    => PointRule::orderBy('order')->get(),
        ]);
    }

    public function toggleStatus(PointRule $pointRule)
    {
        $pointRule->update(['is_active' => !$pointRule->is_active]);

        return response()->json([
            'success' => true,
            'data'    => PointRule::orderBy('order')->get(),
        ]);
    }
}
