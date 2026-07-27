<?php

namespace App\Services;

use App\Models\BukuKas;
use App\Models\JenisBukuKas;
use App\Models\KategoriTransaksi;
use App\Models\TransaksiKas;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganService
{
    /**
     * Build base query untuk transaksi kas dengan filter
     */
    public function buildQuery(array $filters)
    {
        $query = TransaksiKas::query()
            ->join('buku_kas', 'transaksi_kas.buku_kas_id', '=', 'buku_kas.id')
            ->leftJoin('jenis_buku_kas', 'buku_kas.jenis_buku_kas_id', '=', 'jenis_buku_kas.id')
            ->where('buku_kas.is_deleted', 0);

        // Secara default, sembunyikan transaksi transfer antar kas (kas-lain)
        // agar tidak terjadi double counting antara buku anak dan buku induk.
        if (empty($filters['include_rekap'])) {
            $query->where('transaksi_kas.source_type', '!=', 'kas-lain');
        }

        // Filter rentang tanggal
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaksi_kas.tanggal', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('transaksi_kas.tanggal', '<=', $filters['date_to']);
        }

        // Filter per bulan & tahun (shortcut)
        if (!empty($filters['bulan']) && !empty($filters['tahun'])) {
            $query->whereMonth('transaksi_kas.tanggal', $filters['bulan'])
                  ->whereYear('transaksi_kas.tanggal', $filters['tahun']);
        } elseif (!empty($filters['tahun'])) {
            $query->whereYear('transaksi_kas.tanggal', $filters['tahun']);
        }

        // Filter per buku kas
        if (!empty($filters['buku_kas_id'])) {
            $query->where('transaksi_kas.buku_kas_id', $filters['buku_kas_id']);
        }

        // Filter per jenis buku kas
        if (!empty($filters['jenis_buku_kas_id'])) {
            $query->where('buku_kas.jenis_buku_kas_id', $filters['jenis_buku_kas_id']);
        }

        // Filter per kategori
        if (!empty($filters['kategori'])) {
            $query->where('transaksi_kas.kategori', $filters['kategori']);
        }

        // Filter per jenis transaksi (Tunai, QRIS, dll)
        if (!empty($filters['jenis_transaksi'])) {
            $query->where('transaksi_kas.jenis_transaksi', $filters['jenis_transaksi']);
        }

        // Filter tipe (pemasukan/pengeluaran)
        if (!empty($filters['tipe'])) {
            if ($filters['tipe'] === 'pemasukan') {
                $query->where('transaksi_kas.pemasukan', '>', 0);
            } elseif ($filters['tipe'] === 'pengeluaran') {
                $query->where('transaksi_kas.pengeluaran', '>', 0);
            }
        }

        // Filter source_type
        if (!empty($filters['source_type'])) {
            $query->where('transaksi_kas.source_type', $filters['source_type']);
        }

        return $query;
    }

    /**
     * Ringkasan total (header cards)
     */
    public function getSummary(array $filters): array
    {
        $query = $this->buildQuery($filters)
            ->selectRaw('
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->first();

        $pemasukan = (float) ($query->total_pemasukan ?? 0);
        $pengeluaran = (float) ($query->total_pengeluaran ?? 0);

        return [
            'total_pemasukan'  => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo'            => $pemasukan - $pengeluaran,
            'jumlah_transaksi' => (int) ($query->jumlah_transaksi ?? 0),
        ];
    }

    /**
     * Rekap per buku kas
     */
    public function getRekapPerBukuKas(array $filters): array
    {
        return $this->buildQuery($filters)
            ->selectRaw('
                buku_kas.id as buku_kas_id,
                buku_kas.nama as nama_buku_kas,
                jenis_buku_kas.nama as jenis_nama,
                jenis_buku_kas.warna as jenis_warna,
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->groupBy('buku_kas.id', 'buku_kas.nama', 'jenis_buku_kas.nama', 'jenis_buku_kas.warna')
            ->orderByDesc('total_pemasukan')
            ->get()
            ->map(fn($row) => [
                'buku_kas_id'      => $row->buku_kas_id,
                'nama_buku_kas'    => $row->nama_buku_kas,
                'jenis_nama'       => $row->jenis_nama,
                'jenis_warna'      => $row->jenis_warna,
                'total_pemasukan'  => (float) $row->total_pemasukan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'saldo'            => (float) $row->total_pemasukan - (float) $row->total_pengeluaran,
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
            ])
            ->toArray();
    }

    /**
     * Rekap per jenis buku kas
     */
    public function getRekapPerJenisBukuKas(array $filters): array
    {
        return $this->buildQuery($filters)
            ->selectRaw('
                COALESCE(jenis_buku_kas.id, 0) as jenis_id,
                COALESCE(jenis_buku_kas.nama, "Tidak Berkategori") as jenis_nama,
                COALESCE(jenis_buku_kas.warna, "#6b7280") as jenis_warna,
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->groupBy('jenis_buku_kas.id', 'jenis_buku_kas.nama', 'jenis_buku_kas.warna')
            ->orderByDesc('total_pemasukan')
            ->get()
            ->map(fn($row) => [
                'jenis_id'         => $row->jenis_id,
                'jenis_nama'       => $row->jenis_nama,
                'jenis_warna'      => $row->jenis_warna,
                'total_pemasukan'  => (float) $row->total_pemasukan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'saldo'            => (float) $row->total_pemasukan - (float) $row->total_pengeluaran,
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
            ])
            ->toArray();
    }

    /**
     * Rekap per kategori transaksi
     */
    public function getRekapPerKategori(array $filters): array
    {
        return $this->buildQuery($filters)
            ->selectRaw('
                transaksi_kas.kategori,
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->groupBy('transaksi_kas.kategori')
            ->orderByDesc(DB::raw('SUM(transaksi_kas.pemasukan) + SUM(transaksi_kas.pengeluaran)'))
            ->get()
            ->map(fn($row) => [
                'kategori'         => $row->kategori ?: 'Tidak Berkategori',
                'total_pemasukan'  => (float) $row->total_pemasukan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'saldo'            => (float) $row->total_pemasukan - (float) $row->total_pengeluaran,
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
            ])
            ->toArray();
    }

    /**
     * Rekap per jenis transaksi (Tunai, QRIS, dll)
     */
    public function getRekapPerJenisTransaksi(array $filters): array
    {
        return $this->buildQuery($filters)
            ->selectRaw('
                COALESCE(transaksi_kas.jenis_transaksi, "Tidak Tercatat") as jenis_transaksi,
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->groupBy('transaksi_kas.jenis_transaksi')
            ->orderByDesc(DB::raw('SUM(transaksi_kas.pemasukan) + SUM(transaksi_kas.pengeluaran)'))
            ->get()
            ->map(fn($row) => [
                'jenis_transaksi'  => $row->jenis_transaksi,
                'total_pemasukan'  => (float) $row->total_pemasukan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'saldo'            => (float) $row->total_pemasukan - (float) $row->total_pengeluaran,
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
            ])
            ->toArray();
    }

    /**
     * Tren per bulan dalam setahun atau rentang
     */
    public function getTrenBulanan(array $filters): array
    {
        return $this->buildQuery($filters)
            ->selectRaw('
                YEAR(transaksi_kas.tanggal) as tahun,
                MONTH(transaksi_kas.tanggal) as bulan,
                SUM(transaksi_kas.pemasukan) as total_pemasukan,
                SUM(transaksi_kas.pengeluaran) as total_pengeluaran,
                COUNT(transaksi_kas.id) as jumlah_transaksi
            ')
            ->groupBy(DB::raw('YEAR(transaksi_kas.tanggal), MONTH(transaksi_kas.tanggal)'))
            ->orderBy(DB::raw('YEAR(transaksi_kas.tanggal)'))
            ->orderBy(DB::raw('MONTH(transaksi_kas.tanggal)'))
            ->get()
            ->map(fn($row) => [
                'tahun'            => (int) $row->tahun,
                'bulan'            => (int) $row->bulan,
                'label'            => Carbon::create($row->tahun, $row->bulan)->translatedFormat('M Y'),
                'total_pemasukan'  => (float) $row->total_pemasukan,
                'total_pengeluaran' => (float) $row->total_pengeluaran,
                'saldo'            => (float) $row->total_pemasukan - (float) $row->total_pengeluaran,
                'jumlah_transaksi' => (int) $row->jumlah_transaksi,
            ])
            ->toArray();
    }

    /**
     * Daftar transaksi detail dengan pagination dan sorting
     */
    public function getDetailTransaksi(array $filters, int $perPage = 20, string $sortField = 'tanggal', string $sortDir = 'desc')
    {
        $allowedSorts = ['tanggal', 'pemasukan', 'pengeluaran', 'kategori', 'jenis_transaksi'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'tanggal';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        return $this->buildQuery($filters)
            ->selectRaw('
                transaksi_kas.id,
                transaksi_kas.transaction_id,
                transaksi_kas.tanggal,
                transaksi_kas.kategori,
                transaksi_kas.jenis_transaksi,
                transaksi_kas.deskripsi,
                transaksi_kas.pemasukan,
                transaksi_kas.pengeluaran,
                transaksi_kas.source_type,
                buku_kas.id as buku_kas_id,
                buku_kas.nama as nama_buku_kas,
                COALESCE(jenis_buku_kas.nama, "") as jenis_nama,
                COALESCE(jenis_buku_kas.warna, "#6b7280") as jenis_warna
            ')
            ->orderBy('transaksi_kas.' . $sortField, $sortDir)
            ->paginate($perPage)
            ->through(fn($row) => [
                'id'               => $row->id,
                'transaction_id'   => $row->transaction_id,
                'tanggal'          => Carbon::parse($row->tanggal)->format('d M Y'),
                'tanggal_raw'      => $row->tanggal,
                'kategori'         => $row->kategori ?: '-',
                'jenis_transaksi'  => $row->jenis_transaksi ?: '-',
                'deskripsi'        => $row->deskripsi,
                'pemasukan'        => (float) $row->pemasukan,
                'pengeluaran'      => (float) $row->pengeluaran,
                'source_type'      => $row->source_type,
                'buku_kas_id'      => $row->buku_kas_id,
                'nama_buku_kas'    => $row->nama_buku_kas,
                'jenis_nama'       => $row->jenis_nama,
                'jenis_warna'      => $row->jenis_warna,
            ]);
    }

    /**
     * Ambil daftar buku kas aktif untuk filter dropdown
     */
    public function getBukuKasList(): array
    {
        return BukuKas::with('jenisBukuKas')
            ->orderBy('nama')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'nama' => $b->nama,
                'jenis_nama'  => $b->jenisBukuKas->nama ?? null,
                'jenis_warna' => $b->jenisBukuKas->warna ?? null,
            ])
            ->toArray();
    }

    /**
     * Ambil daftar jenis buku kas untuk filter dropdown
     */
    public function getJenisBukuKasList(): array
    {
        return JenisBukuKas::active()->ordered()->get(['id', 'nama', 'kode', 'warna'])->toArray();
    }

    /**
     * Ambil daftar kategori transaksi untuk filter dropdown
     */
    public function getKategoriList(): array
    {
        return KategoriTransaksi::active()->ordered()->get(['id', 'nama', 'jenis'])->toArray();
    }

    /**
     * Ambil daftar jenis transaksi dari data yang ada
     */
    public function getJenisTransaksiList(): array
    {
        return TransaksiKas::distinct()
            ->whereNotNull('jenis_transaksi')
            ->pluck('jenis_transaksi')
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Rentang tahun yang tersedia
     */
    public function getTahunList(): array
    {
        return TransaksiKas::selectRaw('DISTINCT YEAR(tanggal) as tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->map(fn($t) => (int) $t)
            ->toArray();
    }
}
