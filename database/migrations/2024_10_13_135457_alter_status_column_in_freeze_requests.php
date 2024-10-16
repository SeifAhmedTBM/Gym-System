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
        Schema::table('freeze_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE freeze_requests MODIFY COLUMN status ENUM('confirmed', 'pending', 'rejected', 'expired')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freeze_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE freeze_requests MODIFY COLUMN status ENUM('confirmed', 'pending', 'rejected')");
        });
    }
};
