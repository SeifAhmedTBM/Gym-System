<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->string('national')->nullable();
            $table->integer('member_code')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender');
            $table->longText('notes')->nullable();
            $table->boolean('downloaded_app')->default(0);
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
