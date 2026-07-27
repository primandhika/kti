<?php

namespace App\Exports;

use App\Models\BukuKas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class BukuKasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = BukuKas::with('user')->withCount('transaksiKas');

        // Sysadmin dan Head bisa melihat semua buku kas
        if (!$this->user->hasRole('sysadmin') && !$this->user->hasRole('head')) {
            $query->where('user_id', $this->user->id);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Buku Kas',
            'Keterangan',
            'Dibuat',
            'User',
            'Username',
            'Total Pemasukan',
            'Total Pengeluaran',
            'Saldo',
            'Jumlah Transaksi',
        ];
    }

    /**
     * @param mixed $bukuKas
     * @return array
     */
    public function map($bukuKas): array
    {
        static $rowNumber = 1;

        return [
            $rowNumber++,
            $bukuKas->nama,
            $bukuKas->keterangan,
            $bukuKas->created_at->format('d M Y'),
            $bukuKas->user->name,
            $bukuKas->user->username,
            $bukuKas->total_pemasukan,
            $bukuKas->total_pengeluaran,
            $bukuKas->saldo,
            $bukuKas->transaksi_kas_count,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 40,
            'D' => 15,
            'E' => 20,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 18,
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',  // Total Pemasukan
            'H' => '#,##0',  // Total Pengeluaran
            'I' => '#,##0',  // Saldo
        ];
    }
}
