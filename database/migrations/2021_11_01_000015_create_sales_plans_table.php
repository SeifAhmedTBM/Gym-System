<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPlansTable extends Migration
{
    public function up()
    {
        Schema::create('sales_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
