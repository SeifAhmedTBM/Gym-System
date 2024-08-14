<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeslotsTable extends Migration
{
    public function up()
    {
        Schema::create('timeslots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('from')->nullable();
            $table->time('to');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
