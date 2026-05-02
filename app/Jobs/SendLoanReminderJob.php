<?php

namespace App\Jobs;

use App\Models\Loan;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLoanReminderJob implements ShouldQueue
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
        // Get loans that need reminder (return within 3 days)
        $loansNeedingReminder = Loan::where('status', 'dipinjam')
            ->whereBetween('tanggal_kembali_rencana', [
                Carbon::now()->toDateString(),
                Carbon::now()->addDays(3)->toDateString(),
            ])
            ->get();

        foreach ($loansNeedingReminder as $loan) {
            // Send reminder notification
            $this->notificationService->sendReturnReminder($loan);
        }
    }
}
