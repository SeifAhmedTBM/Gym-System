<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id', 'invoice_fk_5245085')->references('id')->on('invoices');
            $table->unsignedBigInteger('sales_by_id');
            $table->foreign('sales_by_id', 'sales_by_fk_5245086')->references('id')->on('users');
        });
    }
}
