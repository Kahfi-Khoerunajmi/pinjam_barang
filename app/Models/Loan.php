<?php

namespace App\Models;

use App\Traits\GeneratesCode;
use App\Traits\HandlesStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use GeneratesCode, HandlesStatus, HasFactory;

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'item_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'status',
        'catatan',
        'dikonfirmasi_oleh',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'status' => 'string',
    ];

    /**
     * Get the user who borrowed the item
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item that was borrowed
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the admin who confirmed this loan
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue(): bool
    {
        return Carbon::now()->gt($this->tanggal_kembali_rencana) &&
               $this->status === 'dipinjam';
    }

    /**
     * Check days until return
     */
    public function daysUntilReturn(): int
    {
        return Carbon::now()->diffInDays($this->tanggal_kembali_rencana);
    }

    /**
     * Check if return is soon (within 3 days)
     */
    public function isReturnSoon(): bool
    {
        return $this->daysUntilReturn() <= 3 && $this->status === 'dipinjam';
    }

    /**
     * Get active loans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Get overdue loans
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'dipinjam')
            ->where('tanggal_kembali_rencana', '<', Carbon::now()->toDateString());
    }

    /**
     * Get returned loans
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    /**
     * Get loans by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
    }

    /**
     * Get pending loans
     */
    public function scopePending($query)
    {
        return $query->where('status', 'diajukan');
    }
}
