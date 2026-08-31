<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {

            $table->id();

            // Nama promo
            $table->string('name');

            // Kode promo
            $table->string('code')
                ->unique();

            // Deskripsi promo
            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | DISCOUNT TYPE
            |--------------------------------------------------------------------------
            |
            | percentage = 10%
            | fixed      = Rp10.000
            |
            */

            $table->enum('discount_type', [
                'percentage',
                'fixed',
            ])->default('percentage');

            // Nilai diskon
            $table->unsignedBigInteger('discount_value');

            // Minimal pembelian
            $table->unsignedBigInteger('minimum_purchase')
                ->default(0);

            // Maksimal potongan untuk percentage
            $table->unsignedBigInteger('maximum_discount')
                ->nullable();

            // Batas penggunaan promo
            $table->unsignedInteger('usage_limit')
                ->nullable();

            // Berapa kali sudah digunakan
            $table->unsignedInteger('used_count')
                ->default(0);

            // Mulai promo
            $table->dateTime('start_at');

            // Berakhir promo
            $table->dateTime('end_at');

            // Status aktif
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('code');

            $table->index('is_active');

            $table->index([
                'start_at',
                'end_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};