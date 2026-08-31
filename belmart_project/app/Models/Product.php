<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'price',
        'stock',
        'weight',
        'image',
        'storage_info',
        'is_best_seller',
        'is_hemat',
        'is_premium',
        'is_active',
        'slug',
        'rating',
        'shelf_life',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'is_best_seller' => 'boolean',
        'is_hemat' => 'boolean',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | FORMAT PRICE
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format(
            $this->price,
            0,
            ',',
            '.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STOCK CHECK
    |--------------------------------------------------------------------------
    */

    public function getIsAvailableAttribute()
    {
        return $this->stock > 0;
    }
}