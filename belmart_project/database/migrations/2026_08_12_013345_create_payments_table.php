<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('order_id')
                ->unique()
                ->constrained('orders')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | PAYMENT METHOD
            |--------------------------------------------------------------------------
            */

            $table->enum('payment_method', [
                'bank_transfer',
                'qris',
                'cash',
            ])->default('qris');

            /*
            |--------------------------------------------------------------------------
            | PAYMENT STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'pending',
                'waiting_verification',
                'verified',
                'rejected',
            ])->default('pending');

            /*
            |--------------------------------------------------------------------------
            | AMOUNT
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('amount');

            /*
            |--------------------------------------------------------------------------
            | PAYMENT PROOF
            |--------------------------------------------------------------------------
            */

            $table->string('proof_image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PAYMENT DATE
            |--------------------------------------------------------------------------
            */

            $table->timestamp('paid_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | ADMIN VERIFICATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | REJECTION REASON
            |--------------------------------------------------------------------------
            */

            $table->text('rejection_reason')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index('status');

            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};