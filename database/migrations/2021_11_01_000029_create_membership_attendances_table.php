<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('membership_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('sign_in')->nullable();
            $table->time('sign_out')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
