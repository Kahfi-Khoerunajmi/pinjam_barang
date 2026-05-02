<?php

namespace App\Http\Controllers;

use App\Exports\ItemsExport;
use App\Exports\LoansExport;
use App\Exports\UsersExport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use AuthorizesRequests;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports page
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $stats = $this->reportService->getDashboardStats();
        $mostBorrowed = $this->reportService->getMostBorrowedItems(10);
        $mostActiveUsers = $this->reportService->getMostActiveUsers(10);

        return view('reports.index', compact('stats', 'mostBorrowed', 'mostActiveUsers'));
    }

    /**
     * Generate loan report
     */
    public function generateLoanReport(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'format' => 'required|in:pdf,excel,view',
        ]);

        $loans = $this->reportService->getLoansReport(
            $validated['start_date'],
            $validated['end_date']
        );

        if ($validated['format'] === 'pdf') {
            return $this->generatePDF('loans', $loans, $validated);
        } elseif ($validated['format'] === 'excel') {
            return Excel::download(
                new LoansExport($loans),
                'barang_dipinjam_'.now()->format('Y-m-d-H-i').'.xlsx'
            );
        }

        return view('reports.loans', compact('loans'));
    }

    /**
     * Generate user activity report
     */
    public function generateUserReport(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $validated = $request->validate([
            'format' => 'required|in:pdf,excel,view',
        ]);

        $users = $this->reportService->getMostActiveUsers(50);

        if ($validated['format'] === 'pdf') {
            return $this->generatePDF('users', $users, $validated);
        } elseif ($validated['format'] === 'excel') {
            return Excel::download(
                new UsersExport($users),
                'pengguna_aktif_'.now()->format('Y-m-d-H-i').'.xlsx'
            );
        }

        return view('reports.users', compact('users'));
    }

    /**
     * Generate item report
     */
    public function generateItemReport(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $validated = $request->validate([
            'format' => 'required|in:pdf,excel,view',
        ]);

        $items = $this->reportService->getMostBorrowedItems(50);

        if ($validated['format'] === 'pdf') {
            return $this->generatePDF('items', $items, $validated);
        } elseif ($validated['format'] === 'excel') {
            return Excel::download(
                new ItemsExport($items),
                'barang_terpopuler_'.now()->format('Y-m-d-H-i').'.xlsx'
            );
        }

        return view('reports.items', compact('items'));
    }

    /**
     * Generate PDF report
     */
    protected function generatePDF($type, $data, $validated)
    {
        $pdf = Pdf::loadView('reports.pdf.'.$type, [
            'data' => $data,
            'generatedAt' => now()->format('d-m-Y H:i:s'),
        ]);

        return $pdf->download('report_'.$type.'_'.now()->format('Y-m-d-H-i').'.pdf');
    }

    /**
     * Overdue summary
     */
    public function overdueSummary()
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $overdueLoans = $this->reportService->getOverdueLoans();

        return view('reports.overdue', compact('overdueLoans'));
    }

    /**
     * Monthly statistics
     */
    public function monthlyStats(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Loan::class);

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $stats = $this->reportService->getMonthlyReport($year, $month);

        return view('reports.monthly', compact('stats'));
    }
}
