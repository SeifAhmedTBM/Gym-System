<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToLeadsTable extends Migration
{
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id', 'status_fk_5244953')->references('id')->on('statuses');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreign('source_id', 'source_fk_5244954')->references('id')->on('sources');
            $table->unsignedBigInteger('sales_by_id')->nullable();
            $table->foreign('sales_by_id', 'sales_by_fk_5244959')->references('id')->on('users');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id', 'address_fk_5244959')->references('id')->on('addresses');
        });
    }
}
