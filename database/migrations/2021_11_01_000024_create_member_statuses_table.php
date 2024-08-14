<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberStatusesTable extends Migration
{
    public function up()
    {
        Schema::create('member_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('default_next_followup_days');
            $table->string('need_followup');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
