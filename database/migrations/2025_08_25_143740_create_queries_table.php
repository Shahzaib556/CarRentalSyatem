<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queries', function (Blueprint $table) {
            $table->id(); // Auto Increment Primary Key
            $table->tinyText('Address')->nullable();
            $table->string('EmailId', 255)->nullable();
            $table->char('ContactNo', 11)->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queries');
    }
};
