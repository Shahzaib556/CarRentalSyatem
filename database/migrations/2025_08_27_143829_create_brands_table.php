<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tblbrands', function (Blueprint $table) {
            $table->id();
            $table->string('BrandName', 120);
            $table->timestamp('CreationDate')->useCurrent();
            $table->timestamp('UpdationDate')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblbrands');
    }
};
