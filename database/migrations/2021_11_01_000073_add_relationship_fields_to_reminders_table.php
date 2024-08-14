<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToRemindersTable extends Migration
{
    public function up()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id', 'lead_fk_5245691')->references('id')->on('leads');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_fk_5245692')->references('id')->on('users');

        });
    }
}
