<?php

namespace App\Services;

use App\Mail\LateReturnMail;
use App\Mail\LoanConfirmationMail;
use App\Mail\LoanReminderMail;
use App\Models\Loan;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send loan confirmation notification
     */
    public function sendLoanConfirmation(Loan $loan)
    {
        if ($loan->status === 'diajukan') {
            $this->createNotification(
                $loan->user_id,
                'Pengajuan Peminjaman',
                'Pengajuan untuk barang '.$loan->item->nama_barang.' telah dikirim dan menunggu persetujuan. Kode: '.$loan->kode_peminjaman,
                'loan',
                $loan->id
            );
        } else {
            // Send email
            Mail::to($loan->user->email)->send(new LoanConfirmationMail($loan));

            // Create in-app notification
            $this->createNotification(
                $loan->user_id,
                'Peminjaman Disetujui',
                'Barang '.$loan->item->nama_barang.' berhasil dipinjam. Kode: '.$loan->kode_peminjaman,
                'loan',
                $loan->id
            );
        }
    }

    /**
     * Send return reminder notification
     */
    public function sendReturnReminder(Loan $loan)
    {
        // Send email
        Mail::to($loan->user->email)->send(new LoanReminderMail($loan));

        // Create in-app notification
        $this->createNotification(
            $loan->user_id,
            'Pengingat Pengembalian',
            'Barang '.$loan->item->nama_barang.' harus dikembalikan pada '.$loan->tanggal_kembali_rencana->format('d/m/Y'),
            'reminder',
            $loan->id
        );
    }

    /**
     * Send late return notification
     */
    public function sendLateReturnNotification(Loan $loan)
    {
        // Send email
        Mail::to($loan->user->email)->send(new LateReturnMail($loan));

        // Create in-app notification
        $this->createNotification(
            $loan->user_id,
            'Barang Terlambat Dikembalikan',
            'Barang '.$loan->item->nama_barang.' telah terlambat dikembalikan. Silakan segera kembalikan.',
            'late',
            $loan->id
        );
    }

    /**
     * Create in-app notification
     */
    public function createNotification($userId, $title, $message, $type = 'general', $relatedId = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_id' => $relatedId,
            'is_read' => false,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['is_read' => true]);

        return $notification;
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get all notifications for user with pagination
     */
    public function getUserNotifications($userId, $perPage = 15)
    {
        return Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
