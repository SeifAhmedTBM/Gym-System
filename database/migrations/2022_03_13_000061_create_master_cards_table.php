<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterCardsTable extends Migration
{
    public function up()
    {
        Schema::create('master_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('master_card')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
