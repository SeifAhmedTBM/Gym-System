<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTemplateDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_template_days', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->boolean('is_offday')->default(0);
            $table->foreignId('schedule_template_id')->constrained('schedule_templates', 'id')->cascadeOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('schedule_template_days');
    }
}
