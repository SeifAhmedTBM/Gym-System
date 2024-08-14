<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipServiceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_service_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_option_pricelist_id')->constrained('service_options_pricelists', 'id')->cascadeOnDelete();
            $table->foreignId('membership_id')->constrained('memberships', 'id')->cascadeOnDelete();
            $table->string('count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_service_options');
    }
}
