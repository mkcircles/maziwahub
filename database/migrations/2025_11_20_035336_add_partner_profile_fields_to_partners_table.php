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
        Schema::table('partners', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('description');
            $table->string('contact_title')->nullable()->after('contact_name');
            $table->string('website')->nullable()->after('contact_title');
            $table->string('country')->nullable()->after('website');
            $table->string('city')->nullable()->after('country');
            $table->string('postal_code', 50)->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn([
                'contact_name',
                'contact_title',
                'website',
                'country',
                'city',
                'postal_code',
            ]);
        });
    }
};
