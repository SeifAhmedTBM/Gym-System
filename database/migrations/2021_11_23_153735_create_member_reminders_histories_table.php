<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRemindersHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_reminders_histories', function (Blueprint $table) {
            $table->id();
            $table->date('due_date')->nullable();
            $table->date('action_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('lead_id')->constrained('leads', 'id')->cascadeOnDelete(); 
            $table->foreignId('member_status_id')->constrained('member_statuses', 'id')->cascadeOnDelete(); 
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
        Schema::dropIfExists('member_reminders_histories');
    }
}
