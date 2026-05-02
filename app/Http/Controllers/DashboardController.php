<?php

namespace App\Http\Controllers;

use App\Services\LoanService;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $reportService;

    protected $loanService;

    public function __construct(ReportService $reportService, LoanService $loanService)
    {
        $this->reportService = $reportService;
        $this->loanService = $loanService;
    }

    /**
     * Display dashboard
     */
    public function index()
    {
        if (Auth::user()->hasRole('admin')) {
            $stats = $this->reportService->getDashboardStats();
            $overdueLoans = $this->loanService->getOverdueLoans();
            $activeLoans = $this->loanService->getActiveLoans();
            
            // New data for dashboard charts
            $topItems = $this->reportService->getMostBorrowedItems(7);
            $monthlyStats = $this->reportService->getMonthlyLoanStats();

            return view('dashboard.index', compact('stats', 'overdueLoans', 'activeLoans', 'topItems', 'monthlyStats'));
        }

        $activeLoans = $this->loanService->getActiveLoans(Auth::id());
        $loanHistory = $this->loanService->getUserLoanHistory(Auth::id());

        $stats = [
            'active_loans' => $activeLoans->count(),
            'total_loans' => $loanHistory->count(),
            'overdue_loans' => $loanHistory->where('status', 'terlambat')->count(),
        ];

        return view('dashboard.index', compact('stats', 'activeLoans', 'loanHistory'));
    }
}
