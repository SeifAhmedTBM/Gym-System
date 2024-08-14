<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToRefundsTable extends Migration
{
    public function up()
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->unsignedBigInteger('refund_reason_id');

            $table->foreign('refund_reason_id', 'refund_reason_fk_5308110')->references('id')->on('refund_reasons');
            $table->unsignedBigInteger('invoice_id');

            $table->foreign('invoice_id', 'invoice_fk_5308111')->references('id')->on('invoices');
            $table->unsignedBigInteger('created_by_id');
            
            $table->foreign('created_by_id', 'created_by_fk_5308113')->references('id')->on('users');
        });
    }
}
