<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            if (!Schema::hasColumn('promos', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('used_count');
            }
            if (!Schema::hasColumn('promos', 'end_date')) {
                $table->timestamp('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('promos', 'quota')) {
                $table->integer('quota')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('promos', 'image')) {
                $table->string('image')->nullable()->after('quota');
            }
        });
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'quota', 'image']);
        });
    }
};
