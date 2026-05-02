<?php

// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getAvailableItemsAttribute()
    {
        return $this->items()->where('status', 'tersedia')->count();
    }
}
