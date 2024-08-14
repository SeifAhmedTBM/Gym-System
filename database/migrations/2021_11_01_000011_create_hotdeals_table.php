<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotdealsTable extends Migration
{
    public function up()
    {
        Schema::create('hotdeals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->string('promo_code')->nullable();
            $table->string('redeem');
            $table->string('type')->nullable();
            $table->longText('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
