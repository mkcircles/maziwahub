<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cow_treatments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('vets')->nullOnDelete();
            $table->foreignId('recorded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('treatment_date');
            $table->string('diagnosis')->nullable();
            $table->string('reason')->nullable();
            $table->string('medication')->nullable();
            $table->string('dosage')->nullable();
            $table->string('dosage_unit')->nullable();
            $table->string('route')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('status')->default('completed');
            $table->string('outcome')->nullable();
            $table->text('next_steps')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('life_cycle_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cow_treatments');
    }
};
