<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_mains', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('session_lists');
            
            $table->unsignedBigInteger('timeslot_id')->nullable();
            $table->foreign('timeslot_id')->references('id')->on('timeslots');
            
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->foreign('trainer_id')->references('id')->on('users');
            
            $table->enum('commission_type',['fixed','percentage'])->default('fixed');
            $table->decimal('commission_amount',15,2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_mains');
    }
}
