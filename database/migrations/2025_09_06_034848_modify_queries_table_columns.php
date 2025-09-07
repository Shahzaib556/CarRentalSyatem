<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change();
            $table->string('EmailId', 255)->nullable(false)->change();
            $table->char('ContactNo', 11)->nullable(false)->change();
            $table->string('message', 255)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('queries', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->change();
            $table->string('EmailId', 255)->nullable()->change();
            $table->char('ContactNo', 11)->nullable()->change();
            $table->string('message', 255)->nullable()->change();
        });
    }
};
