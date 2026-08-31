<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }


    /*
    |--------------------------------------------------------------------------
    | CART TOTAL
    |--------------------------------------------------------------------------
    */

    public function getSubtotal()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }


    public function getTotalItems()
    {
        return $this->items->sum('quantity');
    }
}