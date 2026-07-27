<?php

namespace App\Console\Commands;

use App\Models\Arsip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ScanAndImportArsip extends Command
{
    protected $signature = 'arsip:scan-import
                            {--folder= : Specific folder to scan (optional)}
                            {--dry-run : Show what would be imported without actually importing}';

    protected $description = 'Scan storage directories and import existing files to arsip database';

    private $stats = [
        'total_scanned' => 0,
        'new_imported' => 0,
        'already_exists' => 0,
        'errors' => 0,
    ];

    private $folderMapping = [
        'barangs' => ['kategori' => 'gambar_produk', 'label' => 'Barang'],
        'menu-images' => ['kategori' => 'gambar_produk', 'label' => 'Menu Images'],
        'menu-displays' => ['kategori' => 'gambar_produk', 'label' => 'Menu Displays'],
        'canteen-menus' => ['kategori' => 'gambar_produk', 'label' => 'Canteen Menus'],
        'posts' => ['kategori' => 'gambar_artikel', 'label' => 'Posts'],
        'posts/content' => ['kategori' => 'gambar_artikel', 'label' => 'Posts Content'],
        'pages' => ['kategori' => 'gambar_profil', 'label' => 'Pages'],
        'galeri' => ['kategori' => 'gambar_kegiatan', 'label' => 'Galeri'],
        'work-units' => ['kategori' => 'gambar_profil', 'label' => 'Work Units'],
        'transaction-proofs' => ['kategori' => 'dokumen_resmi', 'label' => 'Transaction Proofs'],
        'shop-items' => ['kategori' => 'gambar_produk', 'label' => 'Shop Items'],
        'bukti-transaksi' => ['kategori' => 'dokumen_resmi', 'label' => 'Bukti Transaksi'],
        'bukti-aktivitas' => ['kategori' => 'dokumen_resmi', 'label' => 'Bukti Aktivitas'],
    ];

    public function handle()
    {
        $this->info('Starting arsip scan and import...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No data will be imported');
            $this->newLine();
        }

        $targetFolder = $this->option('folder');

        if ($targetFolder) {
            if (!isset($this->folderMapping[$targetFolder])) {
                $this->error("Folder '$targetFolder' not found in mapping.");
                $this->info('Available folders: ' . implode(', ', array_keys($this->folderMapping)));
                return 1;
            }
            $this->scanFolder($targetFolder, $dryRun);
        } else {
            foreach ($this->folderMapping as $folder => $config) {
                $this->scanFolder($folder, $dryRun);
            }
        }

        $this->newLine();
        $this->displayStats();

        return 0;
    }

    private function scanFolder($folder, $dryRun = false)
    {
        $config = $this->folderMapping[$folder];
        $path = storage_path('app/public/' . $folder);

        if (!File::exists($path)) {
            $this->warn("Folder tidak ditemukan: {$config['label']} ($folder)");
            return;
        }

        $this->info("Scanning: {$config['label']} ($folder)");

        $files = File::allFiles($path);

        foreach ($files as $file) {
            $this->stats['total_scanned']++;

            // Skip system files
            if ($file->getFilename() === '.gitignore') {
                continue;
            }

            try {
                $relativePath = str_replace(storage_path('app/public/'), '', $file->getPathname());

                // Check if already exists
                $exists = Arsip::where('path', $relativePath)->exists();

                if ($exists) {
                    $this->stats['already_exists']++;
                    $this->line("  <comment>Skip</comment> {$file->getFilename()} (already exists)");
                    continue;
                }

                if (!$dryRun) {
                    $this->importFile($file, $folder, $config);
                    $this->stats['new_imported']++;
                    $this->line("  <info>✓</info> {$file->getFilename()}");
                } else {
                    $this->line("  <fg=cyan>Would import:</> {$file->getFilename()}");
                }

            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error("  ✗ Error importing {$file->getFilename()}: " . $e->getMessage());
            }
        }

        $this->newLine();
    }

    private function importFile($file, $folder, $config)
    {
        $originalName = $file->getFilename();
        $mimeType = mime_content_type($file->getPathname());
        $size = $file->getSize();
        $relativePath = str_replace(storage_path('app/public/'), '', $file->getPathname());

        $jenis = $this->determineJenis($mimeType);

        $originalDimensions = null;
        if (str_starts_with($mimeType, 'image/')) {
            try {
                $imageSize = getimagesize($file->getPathname());
                if ($imageSize) {
                    $originalDimensions = $imageSize[0] . 'x' . $imageSize[1];
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // Auto-generate tags based on folder and filename
        $tags = $this->generateTags($folder, $originalName);

        // Determine if file should be public based on category
        $privateCategories = ['dokumen_resmi', 'dokumen_laporan', 'dokumen_sk', 'dokumen_surat', 'gambar_produk', 'template', 'lainnya'];
        $isPublic = !in_array($config['kategori'], $privateCategories);

        Arsip::create([
            'nama_file' => $originalName,
            'file_name' => $originalName,
            'path' => $relativePath,
            'mime_type' => $mimeType,
            'jenis' => $jenis,
            'kategori_arsip' => $config['kategori'],
            'folder' => $folder,
            'tags' => $tags,
            'deskripsi' => "Auto-imported from {$config['label']}",
            'ukuran' => $size,
            'original_dimensions' => $originalDimensions,
            'is_public' => $isPublic,
            'uploaded_by' => null, // System import
        ]);
    }

    private function determineJenis($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (in_array($mimeType, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ])) {
            return 'document';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        return 'other';
    }

    private function generateTags($folder, $filename)
    {
        $tags = [];

        // Add folder as tag
        $tags[] = $folder;

        // Extract year from filename if exists
        if (preg_match('/20\d{2}/', $filename, $matches)) {
            $tags[] = $matches[0];
        }

        // Add month if exists
        $months = [
            'januari', 'februari', 'maret', 'april', 'mei', 'juni',
            'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
        ];

        foreach ($months as $month) {
            if (stripos($filename, $month) !== false) {
                $tags[] = $month;
                break;
            }
        }

        return $tags;
    }

    private function displayStats()
    {
        $this->info('=== Import Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Files Scanned', $this->stats['total_scanned']],
                ['New Files Imported', $this->stats['new_imported']],
                ['Already Exists (Skipped)', $this->stats['already_exists']],
                ['Errors', $this->stats['errors']],
            ]
        );
    }
}
