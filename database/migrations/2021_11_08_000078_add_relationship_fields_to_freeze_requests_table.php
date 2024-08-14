<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToFreezeRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('freeze_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id');
            $table->foreign('membership_id', 'membership_fk_5292477')->references('id')->on('memberships');
            $table->unsignedBigInteger('created_by_id');
            $table->foreign('created_by_id', 'created_by_fk_5292482')->references('id')->on('users');
        });
    }
}
