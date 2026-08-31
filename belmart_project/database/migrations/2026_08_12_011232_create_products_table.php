<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            // Nama produk
            $table->string('name');

            // Deskripsi produk
            $table->text('description')
                ->nullable();

            // Harga
            $table->unsignedBigInteger('price');

            // Stok
            $table->unsignedInteger('stock')
                ->default(0);

            // Berat produk
            $table->string('weight')
                ->nullable();

            // Gambar produk
            $table->string('image')
                ->nullable();

            // Informasi penyimpanan
            $table->text('storage_info')
                ->nullable();

            // Status produk
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            // Index
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};