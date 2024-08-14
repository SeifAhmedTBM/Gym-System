<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transactionable_type')->nullable();
            $table->integer('transactionable_id')->nullable();
            $table->decimal('amount',15,2);
            $table->foreignId('account_id')->constrained('accounts', 'id')->cascadeOnDelete(); 
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
        Schema::dropIfExists('transactions');
    }
}
