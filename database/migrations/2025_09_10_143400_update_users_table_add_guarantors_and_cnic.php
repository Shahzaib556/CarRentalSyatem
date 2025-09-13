<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns
            $table->string('cnic', 20)->nullable()->after('phone');
            $table->string('guarantor_a_name')->nullable()->after('cnic');
            $table->string('guarantor_a_cnic', 20)->nullable()->after('guarantor_a_name');
            $table->string('guarantor_a_phone', 20)->nullable()->after('guarantor_a_cnic');

            $table->string('guarantor_b_name')->nullable()->after('guarantor_a_phone');
            $table->string('guarantor_b_cnic', 20)->nullable()->after('guarantor_b_name');
            $table->string('guarantor_b_phone', 20)->nullable()->after('guarantor_b_cnic');

            // Drop old column
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollback new columns
            $table->dropColumn([
                'cnic',
                'guarantor_a_name',
                'guarantor_a_cnic',
                'guarantor_a_phone',
                'guarantor_b_name',
                'guarantor_b_cnic',
                'guarantor_b_phone',
            ]);
        });
    }

};
