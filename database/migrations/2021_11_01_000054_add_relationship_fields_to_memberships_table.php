<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToMembershipsTable extends Migration
{
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id', 'member_fk_5244989')->references('id')->on('leads');

            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->foreign('trainer_id', 'trainer_fk_5244990')->references('id')->on('users');

            $table->unsignedBigInteger('service_pricelist_id');
            $table->foreign('service_pricelist_id', 'service_pricelist_fk_5244991')->references('id')->on('pricelists');

            $table->unsignedBigInteger('sales_by_id');
            $table->foreign('sales_by_id', 'sales_by_fk_5244992')->references('id')->on('users');
        });
    }
}
