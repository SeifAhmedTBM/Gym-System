<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainerAttendantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainer_attendants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('leads', 'id')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained('schedules', 'id')->cascadeOnDelete();
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
        Schema::dropIfExists('trainer_attendants');
    }
}
