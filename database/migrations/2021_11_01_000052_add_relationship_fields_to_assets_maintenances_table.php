<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToAssetsMaintenancesTable extends Migration
{
    public function up()
    {
        Schema::table('assets_maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('asset_id');
            $table->foreign('asset_id', 'asset_fk_5245660')->references('id')->on('assets');
            $table->unsignedBigInteger('maintence_vendor_id');
            $table->foreign('maintence_vendor_id', 'maintence_vendor_fk_5245661')->references('id')->on('maintenance_vendors');
        });
    }
}
