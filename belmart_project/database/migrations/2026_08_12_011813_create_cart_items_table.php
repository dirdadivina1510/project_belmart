<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {

            $table->id();

            // Cart
            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            // Produk
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Jumlah produk
            $table->unsignedInteger('quantity');

            // Harga ketika dimasukkan ke cart
            $table->unsignedBigInteger('price');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | PREVENT DUPLICATE PRODUCT
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'cart_id',
                'product_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};