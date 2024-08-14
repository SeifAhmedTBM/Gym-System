<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSessionListsTable extends Migration
{
    public function up()
    {
        Schema::table('session_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id', 'service_fk_5335727')->references('id')->on('services');
        });
    }
}
