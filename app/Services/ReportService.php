<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        return [
            'total_items' => Item::count(),
            'available_items' => Item::where('status', 'tersedia')->count(),
            'loaned_items' => Item::where('status', 'dipinjam')->count(),
            'maintenance_items' => Item::where('status', 'perbaikan')->count(),
            'lost_items' => Item::where('status', 'hilang')->count(),
            'active_loans' => Loan::where('status', 'dipinjam')->count(),
            'overdue_loans' => Loan::overdue()->count(),
            'total_users' => User::count(),
        ];
    }

    /**
     * Get loans report by date range
     */
    public function getLoansReport($startDate, $endDate)
    {
        return Loan::betweenDates($startDate, $endDate)
            ->with('user', 'item', 'admin')
            ->orderByDesc('tanggal_pinjam')
            ->get();
    }

    /**
     * Get most borrowed items
     */
    public function getMostBorrowedItems($limit = 10)
    {
        return Item::withCount('loans')
            ->orderByDesc('loans_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get most active users (peminjam)
     */
    public function getMostActiveUsers($limit = 10)
    {
        return User::withCount('loans')
            ->withCount(['loans as overdue_count' => function ($query) {
                $query->where(function ($q) {
                    $q->where('status', 'terlambat')
                      ->orWhere(function ($subq) {
                          $subq->where('status', 'dipinjam')
                               ->where('tanggal_kembali_rencana', '<', Carbon::now()->toDateString());
                      });
                });
            }])
            ->orderByDesc('loans_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get loan statistics per month
     */
    public function getMonthlyLoanStats($year = null, $month = null)
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        return Loan::whereYear('tanggal_pinjam', $year)
            ->whereMonth('tanggal_pinjam', $month)
            ->selectRaw('DATE(tanggal_pinjam) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get overdue rate
     */
    public function getOverdueRate()
    {
        $totalLoans = Loan::where('status', '!=', 'dipinjam')->count();
        $overdueLoans = Loan::where('status', 'terlambat')->count();

        if ($totalLoans === 0) {
            return 0;
        }

        return round(($overdueLoans / $totalLoans) * 100, 2);
    }

    /**
     * Get category statistics
     */
    public function getCategoryStats()
    {
        return \App\Models\Category::withCount('items')
            ->with(['items' => function ($query) {
                $query->selectRaw('category_id, status, count(*) as count')
                    ->groupBy('category_id', 'status');
            }])
            ->get();
    }

    /**
     * Get user loan history with statistics
     */
    public function getUserLoanStats($userId)
    {
        $user = User::with('loans')->findOrFail($userId);
        $loans = $user->loans;

        return [
            'user' => $user,
            'total_loans' => $loans->count(),
            'active_loans' => $loans->where('status', 'dipinjam')->count(),
            'returned_loans' => $loans->where('status', 'dikembalikan')->count(),
            'overdue_loans' => $loans->where('status', 'terlambat')->count(),
            'lost_items' => $loans->where('status', 'hilang')->count(),
        ];
    }

    /**
     * Get item loan history with statistics
     */
    public function getItemLoanStats($itemId)
    {
        $item = Item::with('loans')->findOrFail($itemId);
        $loans = $item->loans;

        return [
            'item' => $item,
            'total_loans' => $loans->count(),
            'current_borrower' => $item->activeLoan()?->user,
            'last_returned' => $loans->where('status', 'dikembalikan')->latest('tanggal_kembali_aktual')->first(),
        ];
    }

    /**
     * Generate daily report data
     */
    public function getDailyReport($date)
    {
        $date = Carbon::parse($date)->toDateString();

        return [
            'date' => $date,
            'new_loans' => Loan::whereDate('tanggal_pinjam', $date)->count(),
            'returned_loans' => Loan::whereDate('tanggal_kembali_aktual', $date)->count(),
            'overdue_loans' => Loan::overdue()->count(),
        ];
    }

    /**
     * Generate monthly report data
     */
    public function getMonthlyReport($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return [
            'period' => $startDate->format('F Y'),
            'new_loans' => Loan::betweenDates($startDate->toDateString(), $endDate->toDateString())->count(),
            'returned_loans' => Loan::where('status', 'dikembalikan')
                ->betweenDates($startDate->toDateString(), $endDate->toDateString())->count(),
            'overdue_loans' => Loan::overdue()->count(),
            'most_borrowed' => $this->getMostBorrowedItems(5),
        ];
    }

    /**
     * Get overdue loans
     */
    public function getOverdueLoans()
    {
        return Loan::overdue()
            ->with('user', 'item')
            ->orderByDesc('tanggal_kembali_rencana')
            ->get();
    }

    /**
     * Get data for PDF/Excel export
     */
    public function getExportData($startDate, $endDate, $type = 'loans')
    {
        switch ($type) {
            case 'loans':
                return $this->getLoansReport($startDate, $endDate);
            case 'users':
                return $this->getMostActiveUsers();
            case 'items':
                return $this->getMostBorrowedItems();
            default:
                return [];
        }
    }
}
