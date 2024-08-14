<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeAndMembershipIdToReminders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reminders', function (Blueprint $table) {
            $table->enum('type', ['sales', 'follow_up', 'due_payment', 'inactive', 'upgrade', 'expiring', 'custom', 'welcome_call', 'renew', 'pt_session'])->nullable();
            $table->unsignedBigInteger('membership_id')->nullable();
            $table->foreign('membership_id', 'membership_fk_7783208')->references('id')->on('memberships');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reminders', function (Blueprint $table) {
            //
        });
    }
}
