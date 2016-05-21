<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = "recipe";

    public function exists($name, $key, $id = null)
    {
        $recipeCount = $this->where('name', '=', $name)->where('key', '=', $key);
        if(!is_null($id))
        {
            $recipeCount = $recipeCount->where('id', '!=', $id);
        }
        $recipeCount = $recipeCount->count();

        if ($recipeCount > 0) {
            return true;
        }

        return false;
    }

    // FUNCTION FOR GET INGREDIENTS
    public function getIngredients()
    {
        return $this->hasMany('App\Ingredient', 'recipe_id', 'id');
    }
}
