<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulePeriodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_period', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('max_shift');
            $table->integer('min_shift');
            $table->integer('preferred_shift');
            $table->date('starts');
            $table->date('ends');
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
        Schema::dropIfExists('schedule_period');
    }
}
