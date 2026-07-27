<?php

namespace App\Exports;

use App\Models\TransaksiKas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransaksiKasExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $bukuKasId;
    protected $filters;

    public function __construct($bukuKasId, $filters = [])
    {
        $this->bukuKasId = $bukuKasId;
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = TransaksiKas::query()
            ->where('buku_kas_id', $this->bukuKasId)
            ->with('unitKerja');

        // Apply filters
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('tanggal', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('tanggal', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['kategori'])) {
            $query->where('kategori', $this->filters['kategori']);
        }

        if (!empty($this->filters['jenis_transaksi'])) {
            $query->where('jenis_transaksi', $this->filters['jenis_transaksi']);
        }

        if (!empty($this->filters['unit_kerja_id'])) {
            $query->where('unit_kerja_id', $this->filters['unit_kerja_id']);
        }

        if (!empty($this->filters['type'])) {
            if ($this->filters['type'] === 'pemasukan') {
                $query->where('pemasukan', '>', 0);
            } elseif ($this->filters['type'] === 'pengeluaran') {
                $query->where('pengeluaran', '>', 0);
            }
        }

        if (!empty($this->filters['search'])) {
            $query->where('deskripsi', 'like', '%' . $this->filters['search'] . '%');
        }

        // Sorting
        $sortField = $this->filters['sort_field'] ?? 'tanggal';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'ID Transaksi',
            'Tanggal',
            'Kategori',
            'Jenis Transaksi',
            'Unit Kerja',
            'Deskripsi',
            'Pemasukan',
            'Pengeluaran',
        ];
    }

    /**
     * @param mixed $transaksi
     * @return array
     */
    public function map($transaksi): array
    {
        static $rowNumber = 1;

        return [
            $rowNumber++,
            $transaksi->transaction_id,
            $transaksi->tanggal->format('d M Y'),
            $transaksi->kategori,
            $transaksi->jenis_transaksi,
            $transaksi->unitKerja ? $transaksi->unitKerja->name : '-',
            $transaksi->deskripsi,
            $transaksi->pemasukan > 0 ? $transaksi->pemasukan : '',
            $transaksi->pengeluaran > 0 ? $transaksi->pengeluaran : '',
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
            'B' => 18,
            'C' => 15,
            'D' => 20,
            'E' => 25,
            'F' => 25,
            'G' => 50,
            'H' => 18,
            'I' => 18,
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'H' => '#,##0',  // Pemasukan
            'I' => '#,##0',  // Pengeluaran
        ];
    }
}
