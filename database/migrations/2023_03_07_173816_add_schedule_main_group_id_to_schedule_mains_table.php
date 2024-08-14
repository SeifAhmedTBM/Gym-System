<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleMainGroupIdToScheduleMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedule_mains', function (Blueprint $table) {
            $table->unsignedBigInteger('schedule_main_group_id')->nullable();
            $table->foreign('schedule_main_group_id')->references('id')->on('schedule_main_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_mains', function (Blueprint $table) {
            //
        });
    }
}
