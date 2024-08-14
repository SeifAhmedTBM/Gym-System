<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionListsTable extends Migration
{
    public function up()
    {
        Schema::create('session_lists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('max_capacity');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
