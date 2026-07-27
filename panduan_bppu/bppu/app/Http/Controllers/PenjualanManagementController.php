<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Services\PoS\PenjualanTransactionService;
use App\Services\PoS\PenjualanVerificationService;
use App\Services\PoS\TransactionProofService;
use Illuminate\Http\Request;

class PenjualanManagementController extends Controller
{
    protected $transactionService;
    protected $verificationService;
    protected $proofService;

    public function __construct(
        PenjualanTransactionService $transactionService,
        PenjualanVerificationService $verificationService,
        TransactionProofService $proofService
    ) {
        $this->transactionService = $transactionService;
        $this->verificationService = $verificationService;
        $this->proofService = $proofService;
    }

    /**
     * Get transaction history for current user
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $workUnitId = $request->get('work_unit_id');

        $query = Penjualan::with(['workUnit', 'user', 'items.barang'])
            ->where('user_id', $user->id);

        if ($workUnitId) {
            $query->where('work_unit_id', $workUnitId);
        }

        $penjualans = $query->orderBy('tanggal_transaksi', 'desc')
            ->paginate(20);

        return response()->json($penjualans);
    }

    /**
     * Show transaction detail
     */
    public function show($id)
    {
        $penjualan = Penjualan::with(['workUnit', 'user', 'items.barang', 'verifiedBy', 'approvedBy', 'recordedBy'])
            ->findOrFail($id);

        $user = auth()->user();

        // Check if user can access this transaction
        $canAccess = $user->hasRole('sysadmin') ||
                     $user->hasRole('officer') ||
                     (($user->hasRole('canteen') || $user->hasRole('shop')) && $penjualan->user_id === $user->id);

        if (!$canAccess) {
            abort(403, 'Anda tidak diizinkan melihat transaksi ini');
        }

        return response()->json($penjualan);
    }

    /**
     * Cancel transaction - untuk Kantin & Sysadmin
     */
    public function cancel($id)
    {
        $penjualan = Penjualan::with(['items', 'buyer'])->findOrFail($id);
        $user = auth()->user();

        // Check if user can cancel this transaction
        $canCancel = $user->hasRole('sysadmin') ||
                     $user->hasRole('officer') ||
                     (($user->hasRole('canteen') || $user->hasRole('shop')) && $penjualan->user_id === $user->id);

        if (!$canCancel) {
            return back()->with('error', 'Anda tidak diizinkan membatalkan transaksi ini');
        }

        // Check if transaction can be cancelled (e.g., not older than 24 hours)
        if ($penjualan->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Transaksi lebih dari 24 jam tidak dapat dibatalkan');
        }

        $result = $this->transactionService->cancel($penjualan);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['error']);
        }
    }

    /**
     * Verify single transaction - untuk Kantin (transaksi sendiri), Officer & Sysadmin (semua)
     */
    public function verify(Request $request, $id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        $validated = $request->validate([
            'verify_date' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,qris,transfer',
        ]);

        // Check permission
        $canVerify = $user->hasRole('officer') ||
                     $user->hasRole('sysadmin') ||
                     (($user->hasRole('canteen') || $user->hasRole('shop')) && $penjualan->user_id === $user->id);

        if (!$canVerify) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk verifikasi transaksi ini']);
        }

        // Check if already verified
        if ($penjualan->is_verified) {
            return back()->withErrors(['error' => 'Transaksi sudah diverifikasi']);
        }

        $result = $this->verificationService->verify(
            $penjualan,
            $validated['verify_date'],
            $user,
            $validated['metode_pembayaran']
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Verify multiple transactions - untuk Officer & Sysadmin
     */
    public function verifyBulk(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:penjualans,id',
        ]);

        $user = auth()->user();

        // Check permission - Officer, Sysadmin, Kantin, dan Shop bisa bulk verify
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin') && !$user->hasRole('canteen') && !$user->hasRole('shop')) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk verifikasi transaksi']);
        }

        $result = $this->verificationService->verifyBulk($validated['transaction_ids'], $user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Verify all transactions in date range - untuk Officer & Sysadmin
     */
    public function verifyAll(Request $request)
    {
        $validated = $request->validate([
            'work_unit_id' => 'nullable|exists:work_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth()->user();

        // Check permission
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk verifikasi transaksi']);
        }

        $result = $this->verificationService->verifyAll($validated, $user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Approve single transaction - untuk Sysadmin only
     */
    public function approve($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check permission: officer or sysadmin
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Hanya officer atau sysadmin yang dapat approve transaksi']);
        }

        // Check if already approved
        if ($penjualan->is_approved) {
            return back()->withErrors(['error' => 'Transaksi sudah diapprove']);
        }

        // Check if verified first
        if (!$penjualan->is_verified) {
            return back()->withErrors(['error' => 'Transaksi harus diverifikasi terlebih dahulu']);
        }

        $result = $this->verificationService->approve($penjualan, $user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Approve multiple transactions - untuk Officer & Sysadmin
     */
    public function approveBulk(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:penjualans,id',
        ]);

        $user = auth()->user();

        // Check permission: officer or sysadmin
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Hanya officer atau sysadmin yang dapat approve transaksi']);
        }

        $result = $this->verificationService->approveBulk($validated['transaction_ids'], $user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Record to buku kas - untuk Officer & Sysadmin
     */
    public function recordToBukuKas(Request $request, $id)
    {
        $validated = $request->validate([
            'buku_kas_id' => 'required|exists:buku_kas,id',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check permission
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk merekam ke buku kas']);
        }

        // Check if already recorded
        if ($penjualan->is_recorded) {
            return back()->withErrors(['error' => 'Transaksi sudah direkam ke buku kas']);
        }

        // Check if approved first
        if (!$penjualan->is_approved) {
            return back()->withErrors(['error' => 'Transaksi harus diapprove terlebih dahulu']);
        }

        $result = $this->verificationService->recordToBukuKas(
            $penjualan,
            $user,
            $validated['buku_kas_id'],
            $validated['keterangan']
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Assign member buyer to transaction (Officer & Sysadmin only)
     */
    public function assignMember(Request $request, $id)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'buyer_id' => 'required|exists:users,id',
        ]);

        $penjualan = Penjualan::findOrFail($id);

        // Officer/sysadmin bisa assign ke semua transaksi, canteen/shop hanya transaksi sendiri
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            if (!$user->hasRole('canteen') && !$user->hasRole('shop')) {
                return back()->withErrors(['error' => 'Unauthorized']);
            }
            if ($penjualan->user_id !== $user->id) {
                return back()->withErrors(['error' => 'Anda hanya bisa assign member ke transaksi Anda sendiri']);
            }
        }

        // Cannot assign member if already verified
        if ($penjualan->is_verified) {
            return back()->withErrors(['error' => 'Tidak bisa assign member ke transaksi yang sudah diverifikasi']);
        }

        $result = $this->verificationService->assignMember($penjualan, $validated['buyer_id']);

        if ($result['success']) {
            return back();
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Unverify transaction - untuk Officer & Sysadmin (untuk koreksi)
     */
    public function unverify($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check permission
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk unverify transaksi']);
        }

        // Check if not verified
        if (!$penjualan->is_verified) {
            return back()->withErrors(['error' => 'Transaksi belum diverifikasi']);
        }

        // Check if already approved
        if ($penjualan->is_approved) {
            return back()->withErrors(['error' => 'Transaksi sudah diapprove, tidak bisa di-unverify. Silakan unapprove terlebih dahulu.']);
        }

        // Check if already recorded
        if ($penjualan->is_recorded) {
            return back()->withErrors(['error' => 'Transaksi sudah tercatat di buku kas, tidak bisa di-unverify']);
        }

        $result = $this->verificationService->unverify($penjualan);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Unapprove transaction - untuk Sysadmin only (untuk koreksi)
     */
    public function unapprove($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check permission: officer or sysadmin
        if (!$user->hasRole('officer') && !$user->hasRole('sysadmin')) {
            return back()->withErrors(['error' => 'Hanya officer atau sysadmin yang dapat unapprove transaksi']);
        }

        // Check if not approved
        if (!$penjualan->is_approved) {
            return back()->withErrors(['error' => 'Transaksi belum diapprove']);
        }

        // Check if already recorded
        if ($penjualan->is_recorded) {
            return back()->withErrors(['error' => 'Transaksi sudah tercatat di buku kas, tidak bisa di-unapprove. Silakan hapus pencatatan di buku kas terlebih dahulu.']);
        }

        $result = $this->verificationService->unapprove($penjualan);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Upload foto bukti transaksi
     */
    public function uploadFotoBukti(Request $request, $id)
    {
        $validated = $request->validate([
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check if user can access this transaction
        $canAccess = $user->hasRole('sysadmin') ||
                     $user->hasRole('officer') ||
                     (($user->hasRole('canteen') || $user->hasRole('shop')) && $penjualan->user_id === $user->id);

        if (!$canAccess) {
            return back()->withErrors(['error' => 'Anda tidak diizinkan mengupload foto bukti untuk transaksi ini']);
        }

        $result = $this->proofService->uploadFotoBukti($penjualan, $request->file('foto_bukti'));

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }

    /**
     * Delete foto bukti transaksi
     */
    public function deleteFotoBukti($id)
    {
        $penjualan = Penjualan::findOrFail($id);
        $user = auth()->user();

        // Check if user can access this transaction
        $canAccess = $user->hasRole('sysadmin') ||
                     $user->hasRole('officer') ||
                     (($user->hasRole('canteen') || $user->hasRole('shop')) && $penjualan->user_id === $user->id);

        if (!$canAccess) {
            return back()->withErrors(['error' => 'Anda tidak diizinkan menghapus foto bukti untuk transaksi ini']);
        }

        $result = $this->proofService->deleteFotoBukti($penjualan);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['error']]);
        }
    }
}
