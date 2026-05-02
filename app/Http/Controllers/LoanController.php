<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Services\LoanService;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    use AuthorizesRequests;

    protected $loanService;

    protected $notificationService;

    public function __construct(LoanService $loanService, NotificationService $notificationService)
    {
        $this->loanService = $loanService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Loan::class);

        $query = Loan::with('item', 'user', 'admin');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by user (for non-admin)
        if (! Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        // Search by code
        if ($request->filled('search')) {
            $query->where('kode_peminjaman', 'like', '%'.$request->input('search').'%');
        }

        $loans = $query->orderByDesc('tanggal_pinjam')->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $itemId = $request->input('item_id');
        if (!$itemId) {
            abort(403, 'Akses ditolak: Anda harus memilih barang terlebih dahulu.');
        }

        $item = \App\Models\Item::findOrFail($itemId);
        $this->authorize('create', Loan::class);
        $this->authorize('borrow', $item);

        return view('loans.create', compact('item'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Loan::class);

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'tanggal_kembali_rencana' => 'required|date|after:today',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            $item = Item::findOrFail($validated['item_id']);
            $this->authorize('borrow', $item);

            $loan = $this->loanService->createLoan(
                Auth::id(),
                $validated['item_id'],
                $validated['tanggal_kembali_rencana'],
                $validated['catatan'] ?? null
            );

            // Send confirmation notification
            $this->notificationService->sendLoanConfirmation($loan);

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Peminjaman barang berhasil dibuat. Kode: '.$loan->kode_peminjaman);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        $this->authorize('view', $loan);
        $loan->load('item', 'user', 'admin');

        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for returning the item.
     */
    public function returnForm(Loan $loan)
    {
        $this->authorize('return', $loan);

        if ($loan->status !== 'dipinjam') {
            return back()->with('error', 'Barang ini sudah dikembalikan');
        }

        return view('loans.return', compact('loan'));
    }

    /**
     * Return the item.
     */
    public function return(Request $request, Loan $loan)
    {
        $this->authorize('return', $loan);

        try {
            $adminId = Auth::user()->hasRole('admin') ? Auth::id() : null;

            $this->loanService->returnLoan($loan->id, $adminId);

            // Update status if overdue
            if ($loan->isOverdue()) {
                $loan->update(['status' => 'terlambat']);
            }

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Barang berhasil dikembalikan');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve a loan
     */
    public function approve(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan); // Usually only admins can update/approve

        try {
            $this->loanService->approveLoan($loan->id, Auth::id());
            $this->notificationService->sendLoanConfirmation($loan);

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Peminjaman berhasil disetujui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a loan
     */
    public function reject(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan); // Usually only admins can update/reject

        try {
            $this->loanService->rejectLoan($loan->id, Auth::id());
            
            $this->notificationService->createNotification(
                $loan->user_id,
                'Pengajuan Ditolak',
                'Pengajuan peminjaman untuk barang '.$loan->item->nama_barang.' ditolak.',
                'general',
                $loan->id
            );

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Peminjaman berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get active loans for user.
     */
    public function myLoans()
    {
        $loans = $this->loanService->getActiveLoans(Auth::id());
        $loansNeedingReminder = $this->loanService->getLoansNeedingReminder();

        return view('loans.my-loans', compact('loans', 'loansNeedingReminder'));
    }

    /**
     * Get loan history for user.
     */
    public function history(Request $request)
    {
        $userId = Auth::user()->hasRole('admin') && $request->filled('user_id')
            ? $request->input('user_id')
            : Auth::id();

        $loans = $this->loanService->getUserLoanHistory($userId);

        return view('loans.history', compact('loans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        $this->authorize('update', $loan);

        return view('loans.edit', compact('loan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);

        $validated = $request->validate([
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:' . $loan->tanggal_pinjam->toDateString(),
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            $loan->update($validated);

            return redirect()->route('loans.show', $loan)
                ->with('success', 'Data peminjaman berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        $this->authorize('delete', $loan);

        // Only allow deletion of non-completed loans by admin
        if ($loan->status === 'dipinjam') {
            $loan->delete();

            return redirect()->route('loans.index')
                ->with('success', 'Peminjaman berhasil dihapus');
        }

        return back()->with('error', 'Tidak dapat menghapus peminjaman yang sudah dikembalikan');
    }
}
