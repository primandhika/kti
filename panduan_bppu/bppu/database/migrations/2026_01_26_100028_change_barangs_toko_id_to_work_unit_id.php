<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column already renamed
        $columns = Schema::getColumnListing('barangs');
        $hasTokoId = in_array('toko_id', $columns);
        $hasWorkUnitId = in_array('work_unit_id', $columns);

        // If already migrated, skip
        if ($hasWorkUnitId && !$hasTokoId) {
            return;
        }

        // Step 1: Migrate data from tokos to work_units and create mapping
        $tokos = DB::table('tokos')->get();
        $mapping = []; // old toko_id => new work_unit_id

        foreach ($tokos as $toko) {
            // Check if work unit already exists with the same name
            $existingWorkUnit = DB::table('work_units')
                ->where('name', $toko->nama)
                ->first();

            if ($existingWorkUnit) {
                $mapping[$toko->id] = $existingWorkUnit->id;
            } else {
                // Generate unique unit_id
                do {
                    $unitId = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
                } while (DB::table('work_units')->where('unit_id', $unitId)->exists());

                // Insert into work_units
                $workUnitId = DB::table('work_units')->insertGetId([
                    'unit_id' => $unitId,
                    'name' => $toko->nama,
                    'type' => 'toko',
                    'description' => $toko->deskripsi,
                    'location' => $toko->alamat,
                    'contact_phone' => $toko->telepon,
                    'is_active' => $toko->status === 'aktif',
                    'display_order' => 0,
                    'created_at' => $toko->created_at,
                    'updated_at' => $toko->updated_at,
                ]);

                $mapping[$toko->id] = $workUnitId;
            }
        }

        // Step 2: Drop foreign key constraint if exists
        try {
            Schema::table('barangs', function (Blueprint $table) {
                $table->dropForeign(['toko_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
        }

        // Step 3: Update barangs with new work_unit_id values (only if toko_id column exists)
        if ($hasTokoId) {
            foreach ($mapping as $oldTokoId => $newWorkUnitId) {
                DB::table('barangs')
                    ->where('toko_id', $oldTokoId)
                    ->update(['toko_id' => $newWorkUnitId]);
            }
        }

        // Step 4: Rename column from toko_id to work_unit_id (only if toko_id exists)
        if ($hasTokoId && !$hasWorkUnitId) {
            Schema::table('barangs', function (Blueprint $table) {
                $table->renameColumn('toko_id', 'work_unit_id');
            });
        }

        // Step 5: Add new foreign key to work_units (only if not exists)
        try {
            Schema::table('barangs', function (Blueprint $table) {
                $table->foreign('work_unit_id')->references('id')->on('work_units')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['work_unit_id']);

            // Rename column back
            $table->renameColumn('work_unit_id', 'toko_id');
        });

        Schema::table('barangs', function (Blueprint $table) {
            // Add back foreign key to tokos
            $table->foreign('toko_id')->references('id')->on('tokos')->onDelete('cascade');
        });
    }
};
