<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActionToLeadRemindersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_reminders_histories', function (Blueprint $table) {
            $table->enum('action',['appointment','follow_up','maybe','not_interested','no_answer','done'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_reminders_histories', function (Blueprint $table) {
            //
        });
    }
}
