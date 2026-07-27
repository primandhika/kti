<?php

namespace App\Http\Controllers;

use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkUnitController extends Controller
{
    /**
     * Get all work units for settings page
     */
    public function index()
    {
        $workUnits = WorkUnit::orderBy('display_order')->get();
        return response()->json($workUnits);
    }

    /**
     * Store a new work unit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Generate unique unit ID
        $validated['unit_id'] = WorkUnit::generateUnitId();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('work-units', 'public');
        }

        // Set display order to last
        $validated['display_order'] = WorkUnit::max('display_order') + 1;

        $workUnit = WorkUnit::create($validated);

        return response()->json([
            'message' => 'Unit kerja berhasil ditambahkan',
            'workUnit' => $workUnit
        ], 201);
    }

    /**
     * Update an existing work unit
     */
    public function update(Request $request, WorkUnit $workUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'operating_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($workUnit->logo) {
                Storage::disk('public')->delete($workUnit->logo);
            }
            $validated['logo'] = $request->file('logo')->store('work-units', 'public');
        }

        $workUnit->update($validated);

        return response()->json([
            'message' => 'Unit kerja berhasil diperbarui',
            'workUnit' => $workUnit
        ]);
    }

    /**
     * Delete a work unit
     */
    public function destroy(WorkUnit $workUnit)
    {
        // Delete logo if exists
        if ($workUnit->logo) {
            Storage::disk('public')->delete($workUnit->logo);
        }

        $workUnit->delete();

        return response()->json([
            'message' => 'Unit kerja berhasil dihapus'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleActive(WorkUnit $workUnit)
    {
        $workUnit->update([
            'is_active' => !$workUnit->is_active
        ]);

        return response()->json([
            'message' => 'Status unit kerja berhasil diubah',
            'workUnit' => $workUnit
        ]);
    }

    /**
     * Update display order
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:work_units,id',
            'orders.*.display_order' => 'required|integer',
        ]);

        foreach ($validated['orders'] as $order) {
            WorkUnit::where('id', $order['id'])
                ->update(['display_order' => $order['display_order']]);
        }

        return response()->json([
            'message' => 'Urutan unit kerja berhasil diperbarui'
        ]);
    }
}
