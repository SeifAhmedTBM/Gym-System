<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees', 'id')->cascadeOnDelete();
            $table->string('labor_hours')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('basic_salary')->nullable();
            $table->string('gross_salary')->nullable();
            $table->string('overtime')->nullable();
            $table->string('bonus')->nullable();
            $table->string('deduction')->nullable();
            $table->string('loans')->nullable();
            $table->string('delay_deduction')->nullable();
            $table->string('net_salary')->nullable();
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
        Schema::dropIfExists('payrolls');
    }
}
