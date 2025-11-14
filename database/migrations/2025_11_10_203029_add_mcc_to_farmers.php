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
            $table->foreignId('milk_collection_center_id')->nullable()->after('id')->constrained('milk_collection_centers')->nullOnDelete();
            $table->foreignId('registered_by_agent_id')->nullable()->after('registered_by')->constrained('agents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropForeign(['milk_collection_center_id']);
            $table->dropForeign(['registered_by_agent_id']);
            $table->dropColumn('milk_collection_center_id');
            $table->dropColumn('registered_by_agent_id');
        });
    }
};

