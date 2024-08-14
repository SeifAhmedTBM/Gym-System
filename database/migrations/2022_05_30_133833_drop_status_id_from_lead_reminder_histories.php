<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusIdFromLeadReminderHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_reminders_histories', function (Blueprint $table) {
            $table->dropForeign('lead_reminders_histories_status_id_foreign');
            $table->dropColumn('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lead_reminders_histories', function (Blueprint $table) {
            //
        });
    }
}
