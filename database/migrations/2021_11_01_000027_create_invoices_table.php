<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('discount', 15, 2);
            $table->longText('discount_notes')->nullable();
            $table->decimal('service_fee', 15, 2);
            $table->string('payment_method')->nullable();
            $table->decimal('net_amount', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
