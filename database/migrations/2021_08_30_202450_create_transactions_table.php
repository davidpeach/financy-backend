<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('amount');
            $table->string('name');
            $table->timestamp('date');

            $table->unsignedBigInteger('commitment_id')->nullable();
            $table->unsignedBigInteger('account_id');

            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('recipient_type')->nullable();

            $table->integer('closing_balance')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
