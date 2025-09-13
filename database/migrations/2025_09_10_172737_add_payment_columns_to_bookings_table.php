<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('total_days')->after('end_date')->nullable();
            $table->decimal('total_amount', 10, 2)->after('total_days')->nullable();
            $table->decimal('paid_amount', 10, 2)->after('total_amount')->default(0);
            $table->decimal('remaining_amount', 10, 2)->after('paid_amount')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['total_days', 'total_amount', 'paid_amount', 'remaining_amount']);
        });
    }
};

