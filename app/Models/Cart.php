<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    // كل عنصر في السلة يخص مستخدم معين
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // وكل عنصر يخص منتج معين
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

