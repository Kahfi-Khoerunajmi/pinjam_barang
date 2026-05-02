<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckLateReturnsJob implements ShouldQueue
{
    use Queueable;

    protected $notificationService;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all overdue loans
        $overdueLoans = Loan::where('status', 'dipinjam')
            ->where('tanggal_kembali_rencana', '<', Carbon::now()->toDateString())
            ->get();

        foreach ($overdueLoans as $loan) {
            // Update status to terlambat
            $loan->update(['status' => 'terlambat']);

            // Send late return notification
            $this->notificationService->sendLateReturnNotification($loan);
        }
    }
}
