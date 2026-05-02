<?php

namespace App\Models;

use App\Traits\GeneratesCode;
use App\Traits\HandlesStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use GeneratesCode, HandlesStatus, HasFactory;

    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'deskripsi',
        'category_id',
        'lokasi',
        'status',
        'gambar',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the category that owns the item
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all loans for this item
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get the user who created this item
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the active loan for this item
     */
    public function activeLoan()
    {
        return $this->loans()->where('status', 'dipinjam')->latest()->first();
    }

    /**
     * Check if item is available for loan
     */
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }
}
