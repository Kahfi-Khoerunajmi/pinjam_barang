<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesCode
{
    /**
     * Generate unique code for model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-generate kode_barang for items
            if ($model->getTable() === 'items' && empty($model->kode_barang)) {
                $model->generateKodeBarang();
            }
            // Auto-generate kode_peminjaman for loans
            elseif ($model->getTable() === 'loans' && empty($model->kode_peminjaman)) {
                $model->generateKodePeminjaman();
            }
            // Call custom generateCode method if exists
            elseif (method_exists($model, 'generateCode')) {
                $model->generateCode();
            }
        });
    }

    /**
     * Generate kode_barang for Item
     */
    public function generateKodeBarang()
    {
        if ($this->getTable() === 'items') {
            do {
                $code = 'BR-'.Str::upper(Str::random(8));
            } while (\App\Models\Item::where('kode_barang', $code)->exists());
            $this->kode_barang = $code;
        }
    }

    /**
     * Generate kode_peminjaman for Loan
     */
    public function generateKodePeminjaman()
    {
        if ($this->getTable() === 'loans') {
            do {
                $code = 'LN-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
            } while (\App\Models\Loan::where('kode_peminjaman', $code)->exists());
            $this->kode_peminjaman = $code;
        }
    }
}
