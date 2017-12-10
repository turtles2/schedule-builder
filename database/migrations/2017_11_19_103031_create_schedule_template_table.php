<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_template', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employees');
            $table->dateTime('starts');
            $table->dateTime('ends');
            $table->integer('schedule_period')->unsigned();
            $table->foreign('schedule_period')->references('id')->on('schedule_period')->onDelete('cascade');
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
        Schema::dropIfExists('schedule_template');
    }
}
