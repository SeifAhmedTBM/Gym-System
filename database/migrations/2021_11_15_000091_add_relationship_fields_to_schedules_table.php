<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id', 'session_fk_5341504')->references('id')->on('session_lists');
            $table->unsignedBigInteger('timeslot_id');
            $table->foreign('timeslot_id', 'timeslot_fk_5341507')->references('id')->on('timeslots');
            $table->unsignedBigInteger('trainer_id');
            $table->foreign('trainer_id', 'trainer_fk_5341508')->references('id')->on('users');
        });
    }
}
