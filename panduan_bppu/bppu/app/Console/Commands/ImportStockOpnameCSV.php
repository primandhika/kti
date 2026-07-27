<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use App\Models\StockOpname;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\DB;

class ImportStockOpnameCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:stock-opname-csv {file} {work_unit_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stock opname data from CSV file';

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
            $skipped = 0;
            $barangCreated = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }

                // Parse CSV columns
                // Format: Nomor,Tanggal SO,PLU,Deskripsi Barang,COGS,Stock Awal,Penyesuaian,Selisih Quantity,Harga Pokok,Selisih Rupiah
                $nomor = $row[0] ?? null;
                $tanggalSO = $row[1] ?? null;
                $plu = $row[2] ?? null;
                $deskripsiBarang = $row[3] ?? null;
                $cogs = $this->parseRupiah($row[4] ?? '0');
                $stockAwal = intval($row[5] ?? 0);
                $penyesuaian = intval($row[6] ?? 0);
                $selisihQty = intval($row[7] ?? 0);
                $hargaPokok = $this->parseRupiah($row[8] ?? '0');
                $selisihRupiah = $this->parseRupiah($row[9] ?? '0');

                if (empty($plu) || empty($tanggalSO)) {
                    $skipped++;
                    continue;
                }

                // Find or create barang
                $barang = Barang::where('kode_barang', $plu)->first();

                if (!$barang) {
                    $this->warn("Barang dengan PLU {$plu} tidak ditemukan, membuat barang baru...");

                    $barang = Barang::create([
                        'kode_barang' => $plu,
                        'nama_barang' => $deskripsiBarang ?: "Barang {$plu}",
                        'kategori' => 'Import',
                        'satuan' => 'pcs',
                        'stok' => $penyesuaian, // Set stock ke hasil penyesuaian
                        'harga_beli' => $cogs,
                        'harga_jual' => $cogs * 1.2, // Default markup 20%
                        'harga_grosir' => null,
                        'minimum_stok' => 0,
                        'deskripsi' => "Import dari CSV: {$deskripsiBarang}",
                        'work_unit_id' => $workUnitId,
                    ]);

                    $barangCreated++;
                }

                // Parse tanggal (format: 2026-01-05 14:28:28)
                $opnameDate = \Carbon\Carbon::parse($tanggalSO);

                // Get first user ID or null
                $userId = \App\Models\User::first()?->id;

                // Create stock opname record
                StockOpname::create([
                    'barang_id' => $barang->id,
                    'work_unit_id' => $workUnitId,
                    'user_id' => $userId, // Use first user or null
                    'opname_date' => $opnameDate,
                    'stock_awal' => $stockAwal,
                    'stock_fisik' => $penyesuaian,
                    'selisih' => $selisihQty,
                    'harga_pokok' => $hargaPokok ?: $cogs,
                    'selisih_rupiah' => $selisihRupiah,
                    'keterangan' => "Import dari CSV",
                    'status' => 'approved',
                ]);

                $imported++;

                if ($imported % 5 == 0) {
                    $this->info("Progress: {$imported} records diimport...");
                }
            }

            fclose($handle);

            DB::commit();

            $this->info("\n========================================");
            $this->info("Import selesai!");
            $this->info("========================================");
            $this->info("Total records diimport: {$imported}");
            $this->info("Barang baru dibuat: {$barangCreated}");
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
}
