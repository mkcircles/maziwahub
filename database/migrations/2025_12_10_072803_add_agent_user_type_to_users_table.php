<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'agent'
        \DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('super_admin', 'admin', 'partner', 'mcc', 'user', 'agent') NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'agent' from the enum
        \DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('super_admin', 'admin', 'partner', 'mcc', 'user') NOT NULL DEFAULT 'user'");
    }
};
