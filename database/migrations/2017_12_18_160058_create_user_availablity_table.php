<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAvailablityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_availablity', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('available');
            $table->dateTime('starts');
            $table->dateTime('ends');
            $table->integer('user_schedule_template')->unsigned();
            $table->foreign('user_schedule_template')->references('id')->on('user_schedule_template')->onDelete('cascade');
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
        Schema::dropIfExists('user_availablity');
    }
}
