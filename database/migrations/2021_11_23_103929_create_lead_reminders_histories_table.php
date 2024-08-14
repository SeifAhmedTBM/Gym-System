<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadRemindersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_reminders_histories', function (Blueprint $table) {
            $table->id();
            $table->date('due_date')->nullable();
            $table->date('action_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('lead_id')->constrained('leads', 'id')->cascadeOnDelete(); 
            $table->foreignId('status_id')->constrained('statuses', 'id')->cascadeOnDelete(); 
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete(); 
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
        Schema::dropIfExists('lead_reminders_histories');
    }
}
