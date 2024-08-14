<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTiersTable extends Migration
{
    public function up()
    {
        Schema::create('sales_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('month');
            $table->string('type');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
