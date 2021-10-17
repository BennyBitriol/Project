<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Devicelist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devicelist', function (Blueprint $table) {
            $table->string('id');
            $table->string('user_id');
            $table->string('sensor_name');
            $table->string('sensor_enable');
            $table->string('module_id');
            $table->string('icon');
            $table->string('type');
            $table->string('location');
            $table->string('order');
            $table->string('group_id');
            $table->string('extra1');
            $table->string('extra2');
            $table->string('extra3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
