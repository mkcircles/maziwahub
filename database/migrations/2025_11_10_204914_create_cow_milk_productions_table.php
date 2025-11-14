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
        Schema::create('cow_milk_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cow_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_date');
            $table->decimal('morning_volume_liters', 8, 2)->nullable();
            $table->decimal('midday_volume_liters', 8, 2)->nullable();
            $table->decimal('evening_volume_liters', 8, 2)->nullable();
            $table->decimal('total_volume_liters', 8, 2)->nullable();
            $table->string('recorded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['cow_id', 'recorded_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cow_milk_productions');
    }
};
