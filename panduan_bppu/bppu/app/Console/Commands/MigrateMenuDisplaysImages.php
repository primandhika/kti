<?php

namespace App\Console\Commands;

use App\Models\MenuDisplay;
use App\Models\Barang;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateMenuDisplaysImages extends Command
{
    protected $signature = 'menu-displays:migrate-images {--dry-run : Show what would be migrated without actually migrating}';

    protected $description = 'Migrate menu-displays images to storage/barangs with product names';

    private $stats = [
        'total_found' => 0,
        'migrated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->info('Starting menu-displays images migration...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be moved');
            $this->newLine();
        }

        $oldPath = storage_path('app/public/menu-displays');

        if (!File::exists($oldPath)) {
            $this->warn('Folder storage/app/public/menu-displays tidak ditemukan');
            return 0;
        }

        // Ensure storage/barangs exists
        if (!$dryRun && !Storage::disk('public')->exists('barangs')) {
            Storage::disk('public')->makeDirectory('barangs');
        }

        $files = File::files($oldPath);
        $this->stats['total_found'] = count($files);

        if ($this->stats['total_found'] === 0) {
            $this->info('Tidak ada file untuk dimigrasi');
            return 0;
        }

        $this->info("Ditemukan {$this->stats['total_found']} file");
        $this->newLine();

        DB::beginTransaction();

        try {
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $oldFilePath = 'menu-displays/' . $filename;
                $oldFilePathWithSlash = '/storage/menu-displays/' . $filename;

                // Check if file is referenced in menu_displays
                $menuDisplays = MenuDisplay::where('gambar', $oldFilePath)
                    ->orWhere('gambar', $oldFilePathWithSlash)
                    ->get();

                if ($menuDisplays->isEmpty()) {
                    $this->line("  <comment>Skip</comment> {$filename} (tidak digunakan di database)");
                    $this->stats['skipped']++;
                    continue;
                }

                try {
                    foreach ($menuDisplays as $menuDisplay) {
                        $barang = Barang::find($menuDisplay->barang_id);

                        if (!$barang) {
                            $this->warn("  <comment>Skip</comment> {$filename} (barang tidak ditemukan)");
                            $this->stats['skipped']++;
                            continue 2;
                        }

                        $barangName = Str::slug($barang->nama_barang);
                        $extension = $file->getExtension();
                        $newFilename = time() . '_' . $barangName . '.' . $extension;
                        $newFilePath = 'barangs/' . $newFilename;

                        if (!$dryRun) {
                            // Copy file to new location with new name
                            Storage::disk('public')->put($newFilePath, File::get($file->getPathname()));

                            // Update database reference
                            $menuDisplay->gambar = '/storage/' . $newFilePath;
                            $menuDisplay->save();

                            $this->line("  <info>✓</info> {$filename} → {$newFilename} (barang: {$barang->nama_barang})");
                        } else {
                            $this->line("  <fg=cyan>Would migrate:</> {$filename} → {$newFilename} (barang: {$barang->nama_barang})");
                        }
                    }

                    // Delete old file after all references updated
                    if (!$dryRun) {
                        File::delete($file->getPathname());
                    }

                    $this->stats['migrated']++;

                } catch (\Exception $e) {
                    $this->stats['errors']++;
                    $this->error("  ✗ Error migrating {$filename}: " . $e->getMessage());
                }
            }

            if (!$dryRun) {
                DB::commit();
                $this->newLine();
                $this->info('Database references updated successfully');
            } else {
                DB::rollBack();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Migration failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->displayStats();

        if (!$dryRun && $this->stats['migrated'] > 0) {
            $this->newLine();
            $this->info('Migrasi selesai! Jalankan: php artisan arsip:scan-import --folder=barangs');
        }

        return 0;
    }

    private function displayStats()
    {
        $this->info('=== Migration Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files Found', $this->stats['total_found']],
                ['Files Migrated', $this->stats['migrated']],
                ['Files Skipped', $this->stats['skipped']],
                ['Errors', $this->stats['errors']],
            ]
        );
    }
}
