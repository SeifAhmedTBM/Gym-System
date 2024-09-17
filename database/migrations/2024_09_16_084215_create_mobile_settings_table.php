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
        Schema::create('mobile_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('privacy_setting')->nullable();
            $table->text('about_us')->nullable();
            $table->text('rules')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->unsignedBigInteger('pt_service_type')->nullable();
            $table->foreign('pt_service_type')->on('service_types')->references('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_settings');
    }
};
