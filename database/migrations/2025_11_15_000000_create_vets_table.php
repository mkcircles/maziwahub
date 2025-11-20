<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vets', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('license_number')->unique();
            $table->date('license_expiry_date')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('specialization')->nullable();
            $table->string('employer')->nullable();
            $table->foreignId('milk_collection_center_id')->nullable()->constrained('milk_collection_centers')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vets');
    }
};
