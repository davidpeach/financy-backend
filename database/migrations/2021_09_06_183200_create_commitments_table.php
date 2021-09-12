<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitmentsTable extends Migration
{
    public function up()
    {
        Schema::create('commitments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('account_id');

            $table->unsignedBigInteger('recipient_id');
            $table->string('recipient_type');

            $table->string('name');
            $table->integer('amount');
            $table->integer('recurring_date');
            $table->timestamp('start_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commitments');
    }
}
