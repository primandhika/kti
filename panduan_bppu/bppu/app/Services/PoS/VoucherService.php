<?php

namespace App\Services\PoS;

use App\Models\Potongan;
use App\Models\Voucher;
use Illuminate\Support\Str;

class VoucherService
{
    public function generateKodePotongan(): string
    {
        do {
            $kode = 'PTG-' . strtoupper(Str::random(6));
        } while (Potongan::where('kode_potongan', $kode)->exists());

        return $kode;
    }

    public function generateKodeVoucher(string $prefixPotongan): string
    {
        do {
            $kode = $prefixPotongan . '-' . strtoupper(Str::random(6));
        } while (Voucher::where('kode_voucher', $kode)->exists());

        return $kode;
    }

    public function generateVouchers(Potongan $potongan, int $jumlah, int $createdBy): array
    {
        $sisaKuota = $potongan->kuota_voucher - $potongan->vouchers()->count();

        if ($jumlah > $sisaKuota) {
            throw new \Exception("Kuota voucher tidak mencukupi. Sisa kuota: {$sisaKuota}");
        }

        $prefix = strtoupper(substr(str_replace('-', '', $potongan->kode_potongan), 0, 5));
        $vouchers = [];

        for ($i = 0; $i < $jumlah; $i++) {
            $vouchers[] = Voucher::create([
                'kode_voucher' => $this->generateKodeVoucher($prefix),
                'potongan_id' => $potongan->id,
                'status' => 'draft',
                'created_by' => $createdBy,
            ]);
        }

        return $vouchers;
    }

    public function printVouchers(array $voucherIds): int
    {
        return Voucher::whereIn('id', $voucherIds)
            ->where('status', 'draft')
            ->update([
                'status' => 'printed',
                'printed_at' => now(),
            ]);
    }

    public function hitungPotongan(Potongan $potongan, float $totalTransaksi): float
    {
        if ($totalTransaksi < $potongan->minimum_transaksi) {
            return 0;
        }

        if ($potongan->tipe === 'persen') {
            $potonganNilai = $totalTransaksi * ($potongan->nilai / 100);
            if ($potongan->maksimum_potongan) {
                $potonganNilai = min($potonganNilai, $potongan->maksimum_potongan);
            }
            return $potonganNilai;
        }

        return min($potongan->nilai, $totalTransaksi);
    }
}
