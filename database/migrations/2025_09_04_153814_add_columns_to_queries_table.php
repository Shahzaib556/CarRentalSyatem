<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->text('message')->nullable()->after('name');
            $table->dateTime('posting_date')->nullable()->after('message');
            $table->string('status')->default('pending')->after('posting_date'); 
            // you can use enum if you want ('pending', 'resolved', 'closed')
        });
    }

    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            $table->dropColumn(['name', 'message', 'posting_date', 'status']);
        });
    }
};
