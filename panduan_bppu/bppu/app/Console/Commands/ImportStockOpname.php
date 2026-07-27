<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkUnit;
use App\Models\Barang;
use App\Models\StockOpname;
use Carbon\Carbon;

class ImportStockOpname extends Command
{
    protected $signature = 'import:stock-opname {csv_file} {work_unit_id}';
    protected $description = 'Import stock opname dari CSV file';

    public function handle()
    {
        $csvFile = $this->argument('csv_file');
        $workUnitId = $this->argument('work_unit_id');

        // Validate work unit exists
        $workUnit = WorkUnit::find($workUnitId);
        if (!$workUnit) {
            $this->error("Work unit dengan ID {$workUnitId} tidak ditemukan!");
            return 1;
        }

        // Validate CSV file exists
        if (!file_exists($csvFile)) {
            $this->error("File CSV {$csvFile} tidak ditemukan!");
            return 1;
        }

        $this->info("Mengimport stock opname untuk: {$workUnit->name}");
        $this->info("Dari file: {$csvFile}");

        // Open CSV file
        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            $this->error("Gagal membuka file CSV!");
            return 1;
        }

        // Skip header
        fgetcsv($handle);

        $imported = 0;
        $skipped = 0;
        $created_barang = 0;

        // Process each row
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[0]) || $row[0] == '') {
                continue; // Skip empty rows
            }

            $tanggalSO = $row[1];
            $plu = $row[2];
            $deskripsi = $row[3];
            $cogs = $this->parseRupiah($row[4]);
            $stockAwal = (int)$row[5];
            $penyesuaian = (int)$row[6];
            $selisihQty = (int)$row[7];

            // Parse tanggal
            try {
                $opnameDate = Carbon::parse($tanggalSO);
            } catch (\Exception $e) {
                $this->warn("Skip baris {$row[0]}: Tanggal tidak valid - {$tanggalSO}");
                $skipped++;
                continue;
            }

            // Find or create barang
            $barang = Barang::where('work_unit_id', $workUnitId)
                ->where('kode_barang', $plu)
                ->first();

            if (!$barang) {
                // Create new barang
                $barang = Barang::create([
                    'work_unit_id' => $workUnitId,
                    'kode_barang' => $plu,
                    'nama_barang' => $deskripsi,
                    'kategori' => null,
                    'satuan' => 'pcs',
                    'stok' => $penyesuaian,
                    'harga_beli' => $cogs,
                    'harga_jual' => $cogs * 1.2, // markup 20%
                    'minimum_stok' => 5,
                ]);
                $created_barang++;
                $this->info("Created barang: {$plu} - {$deskripsi}");
            }

            // Create stock opname record
            $selisihRupiah = $selisihQty * $cogs;

            // Get first available user
            $userId = \App\Models\User::first()->id ?? null;

            StockOpname::create([
                'barang_id' => $barang->id,
                'work_unit_id' => $workUnitId,
                'user_id' => $userId,
                'opname_date' => $opnameDate,
                'stock_awal' => $stockAwal,
                'stock_fisik' => $penyesuaian,
                'selisih' => $selisihQty,
                'harga_pokok' => $cogs,
                'selisih_rupiah' => $selisihRupiah,
                'keterangan' => "Import dari CSV - Nomor: {$row[0]}",
                'status' => 'approved',
            ]);

            // Update stock barang
            $barang->update(['stok' => $penyesuaian]);

            $imported++;
        }

        fclose($handle);

        $this->info("\n=== IMPORT SELESAI ===");
        $this->info("Total barang baru dibuat: {$created_barang}");
        $this->info("Total stock opname diimport: {$imported}");
        $this->info("Total baris diskip: {$skipped}");

        return 0;
    }

    private function parseRupiah($value)
    {
        // Remove Rp, dots, and commas
        $value = str_replace(['Rp', '.', ','], ['', '', '.'], $value);
        return (float)trim($value);
    }
}
