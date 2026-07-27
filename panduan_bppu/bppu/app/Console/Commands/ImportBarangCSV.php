<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\DB;

class ImportBarangCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:barang-csv {file} {work_unit_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import barang (produk) data from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $workUnitId = $this->argument('work_unit_id');

        // Validate file exists
        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        // Validate work unit exists
        $workUnit = WorkUnit::find($workUnitId);
        if (!$workUnit) {
            $this->error("Work Unit dengan ID {$workUnitId} tidak ditemukan");
            return 1;
        }

        $this->info("Memulai import dari: {$filePath}");
        $this->info("Work Unit: {$workUnit->name}");

        DB::beginTransaction();

        try {
            $handle = fopen($filePath, 'r');

            // Skip header row
            $header = fgetcsv($handle);

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }

                // Parse CSV columns
                // Format: Nomor,Nama Produk,Kode Produk (PLU),Margin,Harga Pokok,Harga Jual,Persediaan,Asal Barang,Tanggal Pembuatan,Tanggal Perubahan,Tanggal di Arsipkan,Produk Turunan
                $nomor = $row[0] ?? null;
                $namaProduk = $row[1] ?? null;
                $plu = $row[2] ?? null;
                $margin = $this->parseRupiah($row[3] ?? '0');
                $hargaPokok = $this->parseRupiah($row[4] ?? '0');
                $hargaJual = $this->parseRupiah($row[5] ?? '0');
                $persediaan = intval($row[6] ?? 0);
                $asalBarang = $row[7] ?? null;

                if (empty($plu) || empty($namaProduk)) {
                    $skipped++;
                    continue;
                }

                // Check if barang already exists (by PLU only, regardless of work unit)
                $barang = Barang::where('kode_barang', $plu)->first();

                $data = [
                    'kode_barang' => $plu,
                    'nama_barang' => $this->cleanProductName($namaProduk),
                    'kategori' => $asalBarang ?: 'Umum',
                    'satuan' => 'pcs',
                    'stok' => $persediaan,
                    'harga_beli' => $hargaPokok,
                    'harga_jual' => $hargaJual,
                    'harga_grosir' => null,
                    'minimum_stok' => 5, // Default minimum stock
                    'deskripsi' => "Import dari CSV - {$asalBarang}",
                    'work_unit_id' => $workUnitId,
                ];

                if ($barang) {
                    // Update existing barang
                    $barang->update($data);
                    $updated++;
                } else {
                    // Create new barang
                    Barang::create($data);
                    $imported++;
                }

                if (($imported + $updated) % 50 == 0) {
                    $this->info("Progress: {$imported} baru, {$updated} diupdate...");
                }
            }

            fclose($handle);

            DB::commit();

            $this->info("\n========================================");
            $this->info("Import selesai!");
            $this->info("========================================");
            $this->info("Barang baru: {$imported}");
            $this->info("Barang diupdate: {$updated}");
            $this->info("Records diskip: {$skipped}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error saat import: " . $e->getMessage());
            $this->error("Line: " . $e->getLine());
            return 1;
        }
    }

    /**
     * Parse rupiah format to numeric value
     */
    private function parseRupiah($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Remove "Rp", ".", and any whitespace
        $cleaned = str_replace(['Rp', '.', ' ', ','], ['', '', '', '.'], $value);

        return floatval($cleaned);
    }

    /**
     * Clean product name by removing asterisks and special characters
     */
    private function cleanProductName($name)
    {
        // Remove leading asterisk and product codes
        $cleaned = preg_replace('/^\*\d+\s+/', '', $name);

        return trim($cleaned);
    }
}
