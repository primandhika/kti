<?php

namespace App\Console\Commands;

use App\Models\MenuDisplay;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MigrateBarangImages extends Command
{
    protected $signature = 'barang:migrate-images {--dry-run : Show what would be migrated without actually migrating}';

    protected $description = 'Migrate barang images from public/images/barang to storage/barangs';

    private $stats = [
        'total_found' => 0,
        'migrated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->info('Starting barang images migration...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be moved');
            $this->newLine();
        }

        $oldPath = public_path('images/barang');

        if (!File::exists($oldPath)) {
            $this->warn('Folder public/images/barang tidak ditemukan');
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
                $oldFilePath = 'images/barang/' . $filename;
                $newFilePath = 'barangs/' . $filename;

                // Check if file is referenced in menu_displays
                $menuDisplays = MenuDisplay::where('gambar', $oldFilePath)
                    ->orWhere('gambar', '/' . $oldFilePath)
                    ->get();

                if ($menuDisplays->isEmpty()) {
                    $this->line("  <comment>Skip</comment> {$filename} (tidak digunakan di database)");
                    $this->stats['skipped']++;
                    continue;
                }

                try {
                    if (!$dryRun) {
                        // Copy file to new location
                        Storage::disk('public')->put($newFilePath, File::get($file->getPathname()));

                        // Update database references
                        foreach ($menuDisplays as $menuDisplay) {
                            $menuDisplay->gambar = '/storage/' . $newFilePath;
                            $menuDisplay->save();
                        }

                        // Delete old file
                        File::delete($file->getPathname());

                        $this->line("  <info>✓</info> {$filename} ({$menuDisplays->count()} referensi)");
                    } else {
                        $this->line("  <fg=cyan>Would migrate:</> {$filename} ({$menuDisplays->count()} referensi)");
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
