<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class DefaultSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set default settings for report if they don't exist
        if (!Setting::where('key', 'kepala_bppu_nama')->exists()) {
            Setting::set('kepala_bppu_nama', 'Kepala BPPU');
        }

        if (!Setting::where('key', 'kepala_bppu_nip')->exists()) {
            Setting::set('kepala_bppu_nip', '');
        }
    }
}
