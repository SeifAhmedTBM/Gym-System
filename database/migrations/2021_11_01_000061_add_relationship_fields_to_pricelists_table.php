<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPricelistsTable extends Migration
{
    public function up()
    {
        Schema::table('pricelists', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id', 'service_fk_5244901')->references('id')->on('services');
        });
    }
}
