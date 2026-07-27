<?php

namespace App\Services;

use App\Models\MemberPoint;
use App\Models\Voucher;

/**
 * Menggabungkan dua sumber "potongan yang diberikan ke pembeli":
 * - Voucher terpakai (Voucher status=used) -> nilai potongan = diskon pada penjualan terkait
 * - Redeem poin (MemberPoint type=redeem)  -> nilai potongan = jumlah poin * kurs
 *
 * Keduanya disatukan menjadi satu daftar "Redeem" agar bisa dicetak sebagai
 * kuitansi dan dicatat ke buku kas oleh unit yang menanggung potongan.
 */
class RedeemService
{
    /**
     * Ambil daftar redeem tergabung (belum dipaginasi).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRedeems(array $filters = [])
    {
        $vouchers = $this->getVoucherRedeems($filters);
        $poin = $this->getPoinRedeems($filters);

        return $vouchers->concat($poin)
            ->sortByDesc('tanggal')
            ->values();
    }

    /**
     * Daftar redeem mode "per campaign":
     * - Voucher digabung per Potongan (campaign) yang BELUM dicatat ke buku kas.
     * - Voucher yang sudah tercatat tetap ditampilkan per baris (agar bisa dibatalkan).
     * - Redeem poin selalu per baris.
     */
    public function getRedeemsByCampaign(array $filters = [])
    {
        $vouchers = $this->getVoucherRedeems($filters);
        $poin = $this->getPoinRedeems($filters);

        // Voucher yang sudah tercatat -> biarkan per baris
        $vouchersRecorded = $vouchers->where('is_recorded', true)->values();

        // Voucher belum tercatat -> grup per campaign (potongan_id)
        $vouchersGrouped = $vouchers->where('is_recorded', false)
            ->groupBy('potongan_id')
            ->map(function ($items, $potonganId) {
                $first = $items->first();
                return [
                    'uid' => 'campaign-' . $potonganId,
                    'source' => 'campaign',
                    'potongan_id' => $potonganId,
                    'id' => $potonganId,
                    'tanggal' => $items->max('tanggal'),
                    'nama_potongan' => $first['nama_potongan'],
                    'jumlah_voucher' => $items->count(),
                    'nilai_potongan' => (float) $items->sum('nilai_potongan'),
                    'voucher_ids' => $items->pluck('id')->values(),
                    'work_unit_id' => $items->pluck('work_unit_id')->filter()->unique()->count() === 1
                        ? $first['work_unit_id'] : null,
                    'work_unit_name' => $items->pluck('work_unit_name')->filter()->unique()->count() === 1
                        ? $first['work_unit_name'] : null,
                    'keterangan' => 'Potongan campaign ' . ($first['nama_potongan'] ?? '-')
                        . ' (' . $items->count() . ' voucher)',
                    'is_recorded' => false,
                ];
            })
            ->values();

        return $vouchersGrouped
            ->concat($vouchersRecorded)
            ->concat($poin)
            ->sortByDesc('tanggal')
            ->values();
    }

    /**
     * Voucher terpakai -> entri redeem.
     */
    protected function getVoucherRedeems(array $filters)
    {
        $query = Voucher::with([
                'potongan:id,nama,tipe,nilai',
                'usedBy:id,name,email',
                'penjualan:id,nomor_transaksi,diskon,work_unit_id,tanggal_transaksi',
                'penjualan.workUnit:id,name',
            ])
            ->where('status', 'used');

        if (!empty($filters['dari'])) {
            $query->whereDate('used_at', '>=', $filters['dari']);
        }
        if (!empty($filters['sampai'])) {
            $query->whereDate('used_at', '<=', $filters['sampai']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('kode_voucher', 'like', "%{$search}%")
                    ->orWhereHas('usedBy', fn ($u) => $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return $query->orderByDesc('used_at')->get()->map(function ($v) {
            return [
                'uid' => 'voucher-' . $v->id,
                'source' => 'voucher',
                'id' => $v->id,
                'tanggal' => $v->used_at,
                'member_name' => $v->usedBy?->name,
                'member_email' => $v->usedBy?->email,
                'kode_voucher' => $v->kode_voucher,
                'potongan_id' => $v->potongan_id,
                'nama_potongan' => $v->potongan?->nama,
                'poin' => null,
                'nilai_potongan' => (float) ($v->penjualan?->diskon ?? 0),
                'nomor_transaksi' => $v->penjualan?->nomor_transaksi,
                'work_unit_id' => $v->penjualan?->work_unit_id,
                'work_unit_name' => $v->penjualan?->workUnit?->name,
                'keterangan' => $v->potongan?->nama
                    ? 'Voucher ' . $v->potongan->nama
                    : 'Voucher terpakai',
                'is_recorded' => $v->transaksi_kas_id !== null,
                'transaksi_kas_id' => $v->transaksi_kas_id,
            ];
        });
    }

    /**
     * Redeem poin member -> entri redeem.
     */
    protected function getPoinRedeems(array $filters)
    {
        $kurs = PointService::getExchangeRate();

        $query = MemberPoint::with([
                'user:id,name,email',
                'penjualan:id,nomor_transaksi,work_unit_id',
                'penjualan.workUnit:id,name',
            ])
            ->where('type', 'redeem');

        if (!empty($filters['dari'])) {
            $query->whereDate('created_at', '>=', $filters['dari']);
        }
        if (!empty($filters['sampai'])) {
            $query->whereDate('created_at', '<=', $filters['sampai']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        return $query->orderByDesc('created_at')->get()->map(function ($r) use ($kurs) {
            $poin = abs($r->points);

            return [
                'uid' => 'poin-' . $r->id,
                'source' => 'poin',
                'id' => $r->id,
                'tanggal' => $r->created_at,
                'member_name' => $r->user?->name,
                'member_email' => $r->user?->email,
                'kode_voucher' => null,
                'nama_potongan' => 'Redeem Poin',
                'poin' => $poin,
                'nilai_potongan' => (float) ($poin * $kurs),
                'nomor_transaksi' => $r->penjualan?->nomor_transaksi,
                'work_unit_id' => $r->penjualan?->work_unit_id,
                'work_unit_name' => $r->penjualan?->workUnit?->name,
                'keterangan' => $r->description ?? 'Redeem poin member',
                'is_recorded' => $r->transaksi_kas_id !== null,
                'transaksi_kas_id' => $r->transaksi_kas_id,
            ];
        });
    }

    /**
     * Statistik total untuk kartu ringkasan.
     */
    public function getStats(array $filters = [])
    {
        $redeems = $this->getRedeems($filters);

        return [
            'total_transaksi' => $redeems->count(),
            'total_voucher' => $redeems->where('source', 'voucher')->count(),
            'total_poin_redeem' => $redeems->where('source', 'poin')->count(),
            'total_nilai' => $redeems->sum('nilai_potongan'),
        ];
    }
}
