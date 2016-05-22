<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIngredientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id')->unsigned();
            $table->char('ndbno', 5);
            $table->string('name', 145);
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 145);
            $table->timestamps();
            $table->foreign('recipe_id')->references('id')->on('recipe');
            $table->unique(['id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ingredient', function (Blueprint $table) {
            $table->dropForeign('ingredient_recipe_id_foreign');
        });
    }
}
