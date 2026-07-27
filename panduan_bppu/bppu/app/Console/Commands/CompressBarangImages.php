<?php

namespace App\Console\Commands;

use App\Services\ImageCompressionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CompressBarangImages extends Command
{
    protected $signature = 'barang:compress-images {--dry-run : Tampilkan ukuran tanpa compress} {--min-kb=150 : Ukuran minimum (KB) yang akan dicompress}';

    protected $description = 'Compress gambar barang yang ukurannya besar di storage/barangs';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $minKB = (int) $this->option('min-kb');

        $this->info('Mengecek gambar di storage/barangs/...');
        if ($dryRun) {
            $this->warn('DRY RUN - tidak ada file yang diubah');
        }
        $this->newLine();

        $files = Storage::disk('public')->files('barangs');

        if (empty($files)) {
            $this->warn('Tidak ada file di barangs/');
            return 0;
        }

        $stats = ['total' => 0, 'compressed' => 0, 'skipped' => 0, 'errors' => 0, 'saved_kb' => 0];
        $stats['total'] = count($files);

        $compression = new ImageCompressionService();

        foreach ($files as $storagePath) {
            $filename = basename($storagePath);
            $sizeKB = round(Storage::disk('public')->size($storagePath) / 1024);

            if ($sizeKB < $minKB) {
                $this->line("  Skip  {$filename} ({$sizeKB} KB - sudah kecil)");
                $stats['skipped']++;
                continue;
            }

            if ($dryRun) {
                $this->line("  <comment>Perlu compress:</comment> {$filename} ({$sizeKB} KB)");
                $stats['compressed']++;
                continue;
            }

            try {
                $fullPath = Storage::disk('public')->path($storagePath);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                // Baca gambar dari disk
                $mimeMap = [
                    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                    'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif',
                ];
                $mime = $mimeMap[$ext] ?? 'image/jpeg';

                // Buat image resource
                $source = match ($mime) {
                    'image/png' => @imagecreatefrompng($fullPath),
                    'image/webp' => @imagecreatefromwebp($fullPath),
                    'image/gif' => @imagecreatefromgif($fullPath),
                    default => @imagecreatefromjpeg($fullPath),
                };

                if (!$source) {
                    $this->error("  Error membaca: {$filename}");
                    $stats['errors']++;
                    continue;
                }

                $origW = imagesx($source);
                $origH = imagesy($source);
                $maxWidth = 800;

                $newW = $origW > $maxWidth ? $maxWidth : $origW;
                $newH = $origW > $maxWidth ? (int) ($origH * ($maxWidth / $origW)) : $origH;

                $resized = imagecreatetruecolor($newW, $newH);
                $white = imagecolorallocate($resized, 255, 255, 255);
                imagefill($resized, 0, 0, $white);
                imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

                $tempPath = sys_get_temp_dir() . '/compress_' . uniqid() . '.jpg';
                $quality = 85;

                do {
                    imagejpeg($resized, $tempPath, $quality);
                    $newKB = round(filesize($tempPath) / 1024);
                    $quality -= 10;
                } while ($newKB > 150 && $quality >= 20);

                // Overwrite file asli (simpan sebagai .jpg)
                $newStoragePath = 'barangs/' . pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                Storage::disk('public')->put($newStoragePath, file_get_contents($tempPath));

                imagedestroy($source);
                imagedestroy($resized);
                @unlink($tempPath);

                $saved = $sizeKB - $newKB;
                $stats['saved_kb'] += $saved;
                $stats['compressed']++;

                $this->line("  OK  {$filename}: {$sizeKB} KB -> {$newKB} KB (hemat {$saved} KB)");

            } catch (\Exception $e) {
                $this->error("  Error: {$filename} - " . $e->getMessage());
                $stats['errors']++;
            }
        }

        $this->newLine();
        $this->info('=== Hasil ===');
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total file', $stats['total']],
                ['Dicompress', $stats['compressed']],
                ['Diskip', $stats['skipped']],
                ['Error', $stats['errors']],
                ['Total hemat', round($stats['saved_kb'] / 1024, 2) . ' MB'],
            ]
        );

        return 0;
    }
}
