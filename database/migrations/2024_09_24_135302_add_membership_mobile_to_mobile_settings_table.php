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
        Schema::table('mobile_settings', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('membership_service_type')->nullable();
            $table->foreign('membership_service_type')->on('service_types')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_settings', function (Blueprint $table) {
            //
            $table->dropColumn('membership_service_type');
        });
    }
};
