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
        Schema::table('farmers', function (Blueprint $table) {
            $table->foreignId('primary_feeding_method_id')
                ->nullable()
                ->after('water_source')
                ->constrained('feeding_methods')
                ->nullOnDelete();

            $table->foreignId('supplemental_feeding_method_id')
                ->nullable()
                ->after('primary_feeding_method_id')
                ->constrained('feeding_methods')
                ->nullOnDelete();

            $table->timestamp('feeding_last_changed_at')
                ->nullable()
                ->after('supplemental_feeding_method_id');

            $table->json('feeding_metadata')
                ->nullable()
                ->after('feeding_last_changed_at');

            $table->text('feeding_notes')
                ->nullable()
                ->after('feeding_metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropForeign(['primary_feeding_method_id']);
            $table->dropForeign(['supplemental_feeding_method_id']);

            $table->dropColumn([
                'primary_feeding_method_id',
                'supplemental_feeding_method_id',
                'feeding_last_changed_at',
                'feeding_metadata',
                'feeding_notes',
            ]);
        });
    }
};
