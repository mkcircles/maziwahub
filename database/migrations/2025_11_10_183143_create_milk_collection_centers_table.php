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
        Schema::create('milk_collection_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->text('physical_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('location')->nullable();
            $table->date('established_date')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('manager_phone')->nullable();
            $table->integer('staff_count')->default(0);
            $table->string('power_source')->nullable();
            $table->integer('cooler_capacity_liters')->nullable();
            $table->boolean('has_testing_equipment')->default(false);
            $table->boolean('has_washing_bay')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_collection_centers');
    }
};
