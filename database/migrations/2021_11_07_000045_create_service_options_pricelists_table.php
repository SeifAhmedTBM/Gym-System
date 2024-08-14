<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOptionsPricelistsTable extends Migration
{
    public function up()
    {
        Schema::create('service_options_pricelists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_option_id');
            $table->foreign('service_option_id', 'service_option_fk_5282789')->references('id')->on('service_options');
            $table->unsignedBigInteger('pricelist_id');
            $table->foreign('pricelist_id', 'pricelist_fk_5282790')->references('id')->on('pricelists');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
