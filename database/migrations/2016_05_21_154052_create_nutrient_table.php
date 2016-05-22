<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNutrientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrient', function (Blueprint $table) {
            $table->increments('id');
            $table->unique('id');
            $table->integer('ingredient_id')->unsigned();
            $table->integer('nutrient_id')->unsigned();
            $table->timestamps();
            $table->foreign('ingredient_id')->references('id')->on('ingredient');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nutrient', function (Blueprint $table) {
            $table->dropForeign('nutrient_ingredient_id_foreign');
        });
    }
}
