<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTiersRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_tiers_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_tier_id')->constrained('sales_tiers', 'id')->cascadeOnDelete();
            $table->decimal('range_from', 15, 2);
            $table->decimal('range_to', 15, 2);
            $table->float('commission', 15, 2);
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
        Schema::dropIfExists('sales_tiers_ranges');
    }
}
