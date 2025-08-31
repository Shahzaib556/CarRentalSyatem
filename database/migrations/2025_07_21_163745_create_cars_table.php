<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tblcars', function (Blueprint $table) {
            $table->id();
            $table->string('CarTitle', 150)->nullable();

            // Foreign key to tblbrands
            $table->unsignedBigInteger('CarBrand')->nullable();
            $table->foreign('CarBrand')->references('id')->on('tblbrands')->onDelete('cascade');

            $table->longText('CarOverview')->nullable();
            $table->integer('PricePerDay')->nullable();
            $table->string('FuelType', 100)->nullable();
            $table->integer('ModelYear')->nullable();
            $table->integer('SeatingCapacity')->nullable();

            // Images
            $table->string('Image1', 120)->nullable();
            $table->string('Image2', 120)->nullable();
            $table->string('Image3', 120)->nullable();
            $table->string('Image4', 120)->nullable();
            $table->string('Image5', 120)->nullable();

            // Features (0 = no, 1 = yes)
            $table->boolean('AirConditioner')->default(0);
            $table->boolean('PowerDoorLocks')->default(0);
            $table->boolean('AntiLockBrakingSystem')->default(0);
            $table->boolean('BrakeAssist')->default(0);
            $table->boolean('PowerSteering')->default(0);
            $table->boolean('DriverAirbag')->default(0);
            $table->boolean('PassengerAirbag')->default(0);
            $table->boolean('PowerWindows')->default(0);
            $table->boolean('CDPlayer')->default(0);
            $table->boolean('CentralLocking')->default(0);
            $table->boolean('CrashSensor')->default(0);
            $table->boolean('LeatherSeats')->default(0);

            // Timestamps
            $table->timestamp('RegDate')->useCurrent();
            $table->timestamp('UpdationDate')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblcars');
    }
};
