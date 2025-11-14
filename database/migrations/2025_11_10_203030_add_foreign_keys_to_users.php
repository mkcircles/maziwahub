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
            $table->foreign('milk_collection_center_id')
                ->references('id')
                ->on('milk_collection_centers')
                ->nullOnDelete();

            $table->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->nullOnDelete();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->foreign('milk_collection_center_id')
                ->references('id')
                ->on('milk_collection_centers')
                ->nullOnDelete();

            $table->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['milk_collection_center_id']);
            $table->dropForeign(['partner_id']);
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign(['milk_collection_center_id']);
            $table->dropForeign(['partner_id']);
        });
    }
};

