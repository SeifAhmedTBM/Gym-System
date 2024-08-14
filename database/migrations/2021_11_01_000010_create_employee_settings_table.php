<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->float('default_month_days', 15, 2);
            $table->float('default_vacation_days', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
