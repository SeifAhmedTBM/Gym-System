<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreezeRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('freeze_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('freeze')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status',['confirmed','pending','rejected']);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
