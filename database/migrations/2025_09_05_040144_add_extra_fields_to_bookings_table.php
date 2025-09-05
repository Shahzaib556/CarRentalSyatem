<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('booking_no')->nullable()->after('id');
            $table->string('useremail')->nullable()->after('user_id');
            $table->text('message')->nullable()->after('status');
            $table->string('receipt')->nullable()->after('message');
            $table->timestamp('postingdate')->nullable()->after('receipt');
            $table->timestamp('updationdate')->nullable()->after('postingdate');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_no',
                'useremail',
                'message',
                'receipt',
                'postingdate',
                'updationdate'
            ]);
        });
    }
};
