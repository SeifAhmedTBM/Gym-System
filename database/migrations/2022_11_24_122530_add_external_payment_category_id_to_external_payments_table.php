<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalPaymentCategoryIdToExternalPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('external_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('external_payment_category_id')->nullable();
            $table->foreign('external_payment_category_id')->references('id')->on('external_payment_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('external_payments', function (Blueprint $table) {
            //
        });
    }
}
