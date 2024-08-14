<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPricelistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pricelists', function (Blueprint $table) {
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->string('upgrade_from')->nullable();
            $table->string('upgrade_to')->nullable();
            $table->string('expiring_date')->nullable();
            $table->string('expiring_session')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pricelists', function (Blueprint $table) {
            //
        });
    }
}
