<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',

        'discount_type',
        'discount_value',

        'minimum_purchase',
        'maximum_discount',

        'quota',

        'start_date',
        'end_date',

        'image',

        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',

        'quota' => 'integer',

        'start_date' => 'datetime',
        'end_date' => 'datetime',

        'is_active' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    public function isCurrentlyActive()
    {
        return $this->is_active
            && now()->between(
                $this->start_date,
                $this->end_date
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DISCOUNT
    |--------------------------------------------------------------------------
    */

    public function calculateDiscount($subtotal)
    {
        if ($subtotal < $this->minimum_purchase) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {

            $discount =
                $subtotal *
                ($this->discount_value / 100);

            if (
                $this->maximum_discount !== null
                &&
                $discount > $this->maximum_discount
            ) {
                $discount = $this->maximum_discount;
            }

            return $discount;
        }

        return min(
            $this->discount_value,
            $subtotal
        );
    }
}