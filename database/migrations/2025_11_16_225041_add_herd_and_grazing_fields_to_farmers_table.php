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
        Schema::table('farmers', function (Blueprint $table): void {
            $table->string('herd_size', 50)->nullable()->after('animal_production');
            $table->string('grazing_type', 100)->nullable()->after('herd_size');
            $table->string('water_source', 150)->nullable()->after('grazing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table): void {
            $table->dropColumn(['herd_size', 'grazing_type', 'water_source']);
        });
    }
};
