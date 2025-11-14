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
        Schema::create('milk_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('milk_collection_center_id')->constrained()->cascadeOnDelete();
            $table->date('delivery_date');
            $table->decimal('volume_liters', 10, 2);
            $table->string('quality_grade')->nullable();
            $table->decimal('fat_content', 5, 2)->nullable();
            $table->decimal('price_per_liter', 10, 2)->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->string('recorded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milk_deliveries');
    }
};
