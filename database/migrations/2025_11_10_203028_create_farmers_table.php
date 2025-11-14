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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->string('farmer_id', 6)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('educational_level')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('id_number')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
            $table->string('sub_county')->nullable();
            $table->string('parish')->nullable();
            $table->string('village')->nullable();
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_contact')->nullable();
            $table->string('next_of_kin_relationship')->nullable();
            $table->unsignedInteger('no_of_household_members')->nullable();
            $table->string('registered_by')->nullable();
            $table->string('photo')->nullable();
            $table->json('coordinates')->nullable();
            $table->string('status')->default('pending');
            $table->text('validation_reason')->nullable();
            $table->string('reg_type')->nullable();
            $table->boolean('household_head')->default(false);
            $table->unsignedInteger('family_size')->nullable();
            $table->unsignedInteger('male_members')->nullable();
            $table->unsignedInteger('female_members')->nullable();
            $table->unsignedInteger('above_18')->nullable();
            $table->unsignedInteger('below_5')->nullable();
            $table->string('financial_instrument')->nullable();
            $table->string('available_energy_source')->nullable();
            $table->decimal('farm_size', 8, 2)->nullable();
            $table->decimal('land_under_use', 8, 2)->nullable();
            $table->string('land_ownership')->nullable();
            $table->string('farming_type')->nullable();
            $table->string('crop_production')->nullable();
            $table->string('animal_production')->nullable();
            $table->boolean('is_farmer_insured')->default(false);
            $table->text('support_needed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
