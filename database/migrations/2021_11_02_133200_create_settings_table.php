<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_lockers')->nullable();
            $table->enum('freeze_duration', ['days', 'weeks']);
            $table->longText('invoice')->nullable();
            $table->text('login_logo')->nullable();
            $table->text('menu_logo')->nullable();
            $table->string('invoice_prefix')->nullable();
            $table->string('member_prefix')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
