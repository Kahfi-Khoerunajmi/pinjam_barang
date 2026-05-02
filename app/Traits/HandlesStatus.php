<?php

namespace App\Traits;

trait HandlesStatus
{
    /**
     * Get display name for status
     */
    public function getStatusLabel(): string
    {
        $statuses = [
            'tersedia' => 'Tersedia',
            'diajukan' => 'Menunggu Persetujuan',
            'dipinjam' => 'Sedang Dipinjam',
            'perbaikan' => 'Dalam Perbaikan',
            'hilang' => 'Hilang',
            'dikembalikan' => 'Dikembalikan',
            'terlambat' => 'Terlambat',
            'ditolak' => 'Ditolak',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class for Bootstrap
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'tersedia' => 'badge-success',
            'diajukan' => 'badge-secondary',
            'dipinjam' => 'badge-warning',
            'perbaikan' => 'badge-info',
            'hilang' => 'badge-danger',
            'dikembalikan' => 'badge-success',
            'terlambat' => 'badge-danger',
            'ditolak' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Get status icon for display
     */
    public function getStatusIcon(): string
    {
        return match ($this->status) {
            'tersedia' => 'fas fa-check-circle',
            'diajukan' => 'fas fa-hourglass-half',
            'dipinjam' => 'fas fa-clock',
            'perbaikan' => 'fas fa-tools',
            'hilang' => 'fas fa-exclamation-circle',
            'dikembalikan' => 'fas fa-undo',
            'terlambat' => 'fas fa-exclamation-triangle',
            'ditolak' => 'fas fa-times-circle',
            default => 'fas fa-circle',
        };
    }

    /**
     * Check if status is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['tersedia', 'dipinjam']);
    }

    /**
     * Check if status is problematic
     */
    public function isProblematic(): bool
    {
        return in_array($this->status, ['terlambat', 'hilang', 'perbaikan']);
    }
}
