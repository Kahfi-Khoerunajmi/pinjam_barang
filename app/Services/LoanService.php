<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Create a new loan
     */
    public function createLoan($userId, $itemId, $tanggalKembaliRencana, $catatan = null)
    {
        return DB::transaction(function () use ($userId, $itemId, $tanggalKembaliRencana, $catatan) {
            $item = Item::findOrFail($itemId);

            // Validasi item tersedia
            if (! $item->isAvailable()) {
                throw new Exception('Barang tidak tersedia untuk dipinjam');
            }

            // Check if user already has a pending request for this item
            $existingPending = Loan::where('user_id', $userId)
                ->where('item_id', $itemId)
                ->where('status', 'diajukan')
                ->exists();

            if ($existingPending) {
                throw new Exception('Anda sudah memiliki pengajuan peminjaman untuk barang ini yang sedang menunggu persetujuan.');
            }

            // Create loan
            $loan = Loan::create([
                'user_id' => $userId,
                'item_id' => $itemId,
                'tanggal_pinjam' => Carbon::now()->toDateString(),
                'tanggal_kembali_rencana' => $tanggalKembaliRencana,
                'status' => 'diajukan', // Initial status is pending
                'catatan' => $catatan,
            ]);

            // Generate code
            $loan->generateKodePeminjaman();
            $loan->save();

            // Do not update item status yet, it stays 'tersedia' until approved.

            return $loan;
        });
    }

    /**
     * Return a loan
     */
    public function returnLoan($loanId, $adminId = null)
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            $loan = Loan::findOrFail($loanId);

            if ($loan->status !== 'dipinjam') {
                throw new Exception('Loan sudah dikembalikan atau dalam status lain');
            }

            // Update loan
            $loan->update([
                'tanggal_kembali_aktual' => Carbon::now()->toDateString(),
                'status' => 'dikembalikan',
                'dikonfirmasi_oleh' => $adminId,
            ]);

            // Update item status
            $loan->item->update(['status' => 'tersedia']);

            return $loan;
        });
    }

    /**
     * Approve a loan request
     */
    public function approveLoan($loanId, $adminId)
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            $loan = Loan::with('item')->findOrFail($loanId);

            if ($loan->status !== 'diajukan') {
                throw new Exception('Hanya pengajuan peminjaman yang dapat disetujui');
            }

            if (!$loan->item->isAvailable()) {
                // If the item is no longer available (e.g. someone else got approved first)
                $loan->update(['status' => 'ditolak']);
                throw new Exception('Barang sudah tidak tersedia (mungkin dipinjam oleh orang lain). Pengajuan otomatis ditolak.');
            }

            // Update loan
            $loan->update([
                'status' => 'dipinjam',
                'tanggal_pinjam' => Carbon::now()->toDateString(), // Set the actual start date to now
                'dikonfirmasi_oleh' => $adminId,
            ]);

            // Update item status
            $loan->item->update(['status' => 'dipinjam']);

            return $loan;
        });
    }

    /**
     * Reject a loan request
     */
    public function rejectLoan($loanId, $adminId)
    {
        return DB::transaction(function () use ($loanId, $adminId) {
            $loan = Loan::findOrFail($loanId);

            if ($loan->status !== 'diajukan') {
                throw new Exception('Hanya pengajuan peminjaman yang dapat ditolak');
            }

            // Update loan
            $loan->update([
                'status' => 'ditolak',
                'dikonfirmasi_oleh' => $adminId,
            ]);

            // Item remains available

            return $loan;
        });
    }

    /**
     * Get active loans
     */
    public function getActiveLoans($userId = null)
    {
        $query = Loan::with('item', 'user')->whereIn('status', ['dipinjam', 'diajukan']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderByDesc('tanggal_pinjam')->get();
    }

    /**
     * Get overdue loans
     */
    public function getOverdueLoans()
    {
        return Loan::overdue()
            ->with('item', 'user')
            ->orderByDesc('tanggal_kembali_rencana')
            ->get();
    }

    /**
     * Get loans needing reminder (return dalam 3 hari)
     */
    public function getLoansNeedingReminder()
    {
        return Loan::where('status', 'dipinjam')
            ->whereBetween('tanggal_kembali_rencana', [
                Carbon::now()->toDateString(),
                Carbon::now()->addDays(3)->toDateString(),
            ])
            ->with('item', 'user')
            ->get();
    }

    /**
     * Get loan history per user
     */
    public function getUserLoanHistory($userId)
    {
        return Loan::where('user_id', $userId)
            ->with('item', 'admin')
            ->orderByDesc('tanggal_pinjam')
            ->get();
    }

    /**
     * Get loan history per item
     */
    public function getItemLoanHistory($itemId)
    {
        return Loan::where('item_id', $itemId)
            ->with('user', 'admin')
            ->orderByDesc('tanggal_pinjam')
            ->get();
    }

    /**
     * Get loan by code
     */
    public function getLoanByCode($code)
    {
        return Loan::where('kode_peminjaman', $code)
            ->with('item', 'user', 'admin')
            ->firstOrFail();
    }

    /**
     * Calculate total loan days
     */
    public function calculateLoanDays($loan): int
    {
        if ($loan->tanggal_kembali_aktual) {
            return $loan->tanggal_kembali_aktual->diffInDays($loan->tanggal_pinjam);
        }

        return Carbon::now()->diffInDays($loan->tanggal_pinjam);
    }

    /**
     * Calculate overdue days
     */
    public function calculateOverdueDays($loan): int
    {
        if ($loan->status !== 'dipinjam' || ! $loan->isOverdue()) {
            return 0;
        }

        return Carbon::now()->diffInDays($loan->tanggal_kembali_rencana);
    }
}
