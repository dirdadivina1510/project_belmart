<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'transaction_id',
        'amount',
        'status',
        'proof_image',
        'payment_proof',
        'paid_at',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function getPaymentProofAttribute()
    {
        return $this->attributes['payment_proof'] ?? $this->attributes['proof_image'] ?? null;
    }

    public function setPaymentProofAttribute($value)
    {
        $this->attributes['payment_proof'] = $value;
        $this->attributes['proof_image'] = $value;
    }

    public function getProofImageAttribute()
    {
        return $this->attributes['proof_image'] ?? $this->attributes['payment_proof'] ?? null;
    }

    public function setProofImageAttribute($value)
    {
        $this->attributes['proof_image'] = $value;
        $this->attributes['payment_proof'] = $value;
    }

    public function getAdminNoteAttribute()
    {
        return $this->attributes['admin_note'] ?? $this->attributes['rejection_reason'] ?? null;
    }

    public function setAdminNoteAttribute($value)
    {
        $this->attributes['admin_note'] = $value;
        $this->attributes['rejection_reason'] = $value;
    }

    public function getRejectionReasonAttribute()
    {
        return $this->attributes['rejection_reason'] ?? $this->attributes['admin_note'] ?? null;
    }

    public function setRejectionReasonAttribute($value)
    {
        $this->attributes['rejection_reason'] = $value;
        $this->attributes['admin_note'] = $value;
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS CHECK
    |--------------------------------------------------------------------------
    */

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }
}