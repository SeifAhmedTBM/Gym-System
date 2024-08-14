<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMembershipIdToMembershipAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id');
            $table->foreign('membership_id', 'membership_fk_5245025')->references('id')->on('memberships')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_attendances', function (Blueprint $table) {
            //
        });
    }
}
