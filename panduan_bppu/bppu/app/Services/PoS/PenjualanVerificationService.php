<?php

namespace App\Services\PoS;

use App\Models\Penjualan;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;

class PenjualanVerificationService
{
    protected $pointService;

    public function __construct()
    {
        $this->pointService = new PointService();
    }

    /**
     * Verify single transaction
     */
    public function verify(Penjualan $penjualan, string $verifyDate, User $user, string $metodePembayaran = null)
    {
        // Validate date: Non-sysadmin hanya bisa verifikasi untuk hari ini atau setelahnya
        if (!$user->hasRole('sysadmin')) {
            $today = now()->startOfDay();
            $selectedDate = \Carbon\Carbon::parse($verifyDate)->startOfDay();

            if ($selectedDate->lt($today)) {
                return ['success' => false, 'error' => 'Hanya sysadmin yang bisa verifikasi untuk tanggal sebelumnya'];
            }
        }

        // Update tanggal transaksi sesuai pilihan verifikasi
        $newTransactionDate = \Carbon\Carbon::parse($verifyDate)->setTimeFromTimeString(
            \Carbon\Carbon::parse($penjualan->tanggal_transaksi)->format('H:i:s')
        );

        // Prepare update data
        $updateData = [
            'tanggal_transaksi' => $newTransactionDate,
            'is_verified' => true,
            'verified_by' => $user->id,
            'verified_at' => now(),
        ];

        // Update metode pembayaran jika diberikan
        if ($metodePembayaran) {
            $updateData['metode_pembayaran'] = $metodePembayaran;
        }

        // Verify
        $penjualan->update($updateData);

        return [
            'success' => true,
            'message' => 'Transaksi berhasil diverifikasi untuk tanggal ' . \Carbon\Carbon::parse($verifyDate)->format('d/m/Y')
        ];
    }

    /**
     * Verify multiple transactions
     */
    public function verifyBulk(array $transactionIds, User $user)
    {
        DB::beginTransaction();
        try {
            $query = Penjualan::whereIn('id', $transactionIds)
                ->where('is_verified', false)
                ->where('status', 'selesai');

            // Kantin dan Shop hanya bisa verify transaksi mereka sendiri
            if (($user->hasRole('canteen') || $user->hasRole('shop')) && !$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
                $query->where('user_id', $user->id);
            }

            $count = $query->update([
                'is_verified' => true,
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

            DB::commit();

            return ['success' => true, 'message' => "{$count} transaksi berhasil diverifikasi"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal verifikasi transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Verify all transactions in date range
     */
    public function verifyAll(array $params, User $user)
    {
        DB::beginTransaction();
        try {
            $query = Penjualan::whereBetween('tanggal_transaksi', [
                    $params['start_date'] . ' 00:00:00',
                    $params['end_date'] . ' 23:59:59'
                ])
                ->where('is_verified', false)
                ->where('status', 'selesai');

            if (isset($params['work_unit_id'])) {
                $query->where('work_unit_id', $params['work_unit_id']);
            }

            $count = $query->update([
                'is_verified' => true,
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);

            DB::commit();

            return ['success' => true, 'message' => "{$count} transaksi berhasil diverifikasi"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal verifikasi transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Unverify transaction
     */
    public function unverify(Penjualan $penjualan)
    {
        $penjualan->update([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return ['success' => true, 'message' => 'Transaksi berhasil di-unverify. Silakan lakukan koreksi jika diperlukan.'];
    }

    /**
     * Approve single transaction
     */
    public function approve(Penjualan $penjualan, User $user)
    {
        DB::beginTransaction();
        try {
            // Approve
            $penjualan->update([
                'is_approved' => true,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Add points to buyer if buyer is member
            if ($penjualan->buyer && $penjualan->buyer->hasRole('buyer')) {
                $this->pointService->addPointsFromTransaction($penjualan->buyer, $penjualan);
            }

            DB::commit();
            return ['success' => true, 'message' => 'Transaksi berhasil diapprove'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal approve transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Approve multiple transactions
     */
    public function approveBulk(array $transactionIds, User $user)
    {
        DB::beginTransaction();
        try {
            // Get transactions yang akan diapprove
            $penjualans = Penjualan::with('buyer')
                ->whereIn('id', $transactionIds)
                ->where('is_verified', true)
                ->where('is_approved', false)
                ->where('status', 'selesai')
                ->get();

            $count = 0;

            foreach ($penjualans as $penjualan) {
                // Approve transaction
                $penjualan->update([
                    'is_approved' => true,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                ]);

                // Add points to buyer if buyer is member
                if ($penjualan->buyer && $penjualan->buyer->hasRole('buyer')) {
                    $this->pointService->addPointsFromTransaction($penjualan->buyer, $penjualan);
                }

                $count++;
            }

            DB::commit();

            return ['success' => true, 'message' => "{$count} transaksi berhasil diapprove"];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal approve transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Unapprove transaction
     */
    public function unapprove(Penjualan $penjualan)
    {
        DB::beginTransaction();
        try {
            // Reverse points jika ada buyer dan sudah mendapat poin
            if ($penjualan->buyer && $penjualan->buyer->hasRole('buyer')) {
                // Cari member points yang terkait dengan transaksi ini
                $memberPoints = \App\Models\MemberPoint::where('penjualan_id', $penjualan->id)
                    ->where('type', 'earn')
                    ->get();

                foreach ($memberPoints as $point) {
                    // Buat entry reversal
                    \App\Models\MemberPoint::create([
                        'user_id' => $penjualan->buyer->id,
                        'points' => -$point->points,
                        'type' => 'correction',
                        'transaction_amount' => $penjualan->total,
                        'penjualan_id' => $penjualan->id,
                        'description' => "Koreksi (unapprove) - reversal {$point->points} poin dari transaksi {$penjualan->nomor_transaksi}",
                    ]);

                    // Update total points
                    $penjualan->buyer->total_points -= $point->points;
                    $penjualan->buyer->save();
                }
            }

            // Unapprove
            $penjualan->update([
                'is_approved' => false,
                'approved_by' => null,
                'approved_at' => null,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'Transaksi berhasil di-unapprove dan poin telah di-reverse. Silakan lakukan koreksi jika diperlukan.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal unapprove transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Record to buku kas
     */
    public function recordToBukuKas(Penjualan $penjualan, User $user, $bukuKasId, $keterangan = null)
    {
        DB::beginTransaction();
        try {
            // Validasi buku kas
            $bukuKas = \App\Models\BukuKas::findOrFail($bukuKasId);

            // Buat transaksi kas
            $transaksi = \App\Models\TransaksiKas::create([
                'buku_kas_id' => $bukuKas->id,
                'tanggal' => $penjualan->tanggal_transaksi ?? now(),
                'jenis_transaksi' => 'pemasukan',
                'kategori' => 'Penjualan PoS',
                'deskripsi' => $keterangan ?: "Penjualan PoS - {$penjualan->nomor_transaksi}",
                'pemasukan' => $penjualan->total,
                'pengeluaran' => 0,
                'source_type' => 'penjualan',
                'transaction_id' => $penjualan->nomor_transaksi,
                'unit_kerja_id' => $penjualan->work_unit_id,
            ]);

            // Update penjualan
            $penjualan->update([
                'is_recorded' => true,
                'recorded_by' => $user->id,
                'recorded_at' => now(),
                'transaksi_kas_id' => $transaksi->id,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'Transaksi berhasil dicatat ke buku kas'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => 'Gagal mencatat transaksi: ' . $e->getMessage()];
        }
    }

    /**
     * Assign member buyer to transaction
     */
    public function assignMember(Penjualan $penjualan, $buyerId)
    {
        // Verify buyer has buyer role
        $buyer = User::findOrFail($buyerId);
        if (!$buyer->hasRole('buyer')) {
            return ['success' => false, 'error' => 'User bukan member buyer'];
        }

        $penjualan->buyer_id = $buyerId;
        $penjualan->save();

        return ['success' => true];
    }
}
