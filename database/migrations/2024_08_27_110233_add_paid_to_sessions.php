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
        Schema::table('session_lists', function (Blueprint $table) {
            $table->boolean('paid')->nullable()->default(0)->comment('0 = not paid , 1 = paid')->after('max_capacity');
        });
    }
  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            //
        });
    }
};
