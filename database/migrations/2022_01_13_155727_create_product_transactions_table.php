<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products', 'id')->cascadeOnDelete(); 
            $table->unsignedBigInteger('from_warehouse')->nullable();
            $table->foreign('from_warehouse', 'warehouse_fk_9083107')->references('id')->on('warehouses');

            $table->unsignedBigInteger('to_warehouse')->nullable();
            $table->foreign('to_warehouse', 'warehouse_fk_9983107')->references('id')->on('warehouses');

            // $table->foreignId('from_warehouse')->constrained('warehouses', 'id')->cascadeOnDelete()->nullable(); 
            // $table->foreignId('to_warehouse')->constrained('warehouses', 'id')->cascadeOnDelete()->nullable(); 

            $table->enum('type',['in','out','transfer']);
            $table->integer('quantity')->default(0);
            $table->foreignId('created_by')->constrained('users', 'id')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_transactions');
    }
}
