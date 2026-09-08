<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'promo_code')) {
                $table->string('promo_code')->nullable()->after('promo_id');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->unsignedBigInteger('shipping_cost')->default(0)->after('discount');
            }
            if (!Schema::hasColumn('orders', 'notes') && !Schema::hasColumn('orders', 'note')) {
                $table->text('note')->nullable()->after('shipping_address');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('proof_image');
            }
            if (!Schema::hasColumn('payments', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('rejection_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'promo_code')) {
                $table->dropColumn('promo_code');
            }
            if (Schema::hasColumn('orders', 'shipping_cost')) {
                $table->dropColumn('shipping_cost');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'payment_proof')) {
                $table->dropColumn('payment_proof');
            }
            if (Schema::hasColumn('payments', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
        });
    }
};
