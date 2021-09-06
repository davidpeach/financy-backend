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

            $table->string('name');
            $table->integer('amount');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commitments');
    }
}
