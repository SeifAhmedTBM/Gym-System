<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePopMessageRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pop_message_replies', function (Blueprint $table) {
            $table->id();
            $table->string('reply')->nullable();
            $table->foreignId('pop_message_id')->constrained('pop_messages','id')->cascadeOnDelete();
            $table->foreignId('created_by_id')->constrained('users','id')->cascadeOnDelete();
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
        Schema::dropIfExists('pop_message_replies');
    }
}
