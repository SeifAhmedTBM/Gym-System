<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id');
            $table->foreign('membership_id', 'membership_fk_5245056')->references('id')->on('memberships');
            $table->unsignedBigInteger('sales_by_id');
            $table->foreign('sales_by_id', 'sales_by_fk_5245057')->references('id')->on('users');
        });
    }
}
