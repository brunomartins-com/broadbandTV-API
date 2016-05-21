<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = "recipe";

    // FUNCTION FOR GET INGREDIENTS
    public function getIngredients()
    {
        return $this->hasMany('App\Ingredient', 'recipe_id', 'id');
    }
}
