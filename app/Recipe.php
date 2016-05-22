<?php

namespace App;

use Faker\UniqueGenerator;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = "recipe";

    /**
     * Check if Recipe already exists for especified user
     *
     * @param Name of Recipe
     * @param User Unique Key
     * @param Recipe ID
     * 
     * @return bool
     */
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

    /**
     * Get all recipes from user (identified by key)
     *
     * @param User Unique Key
     *
     * @return mixed
     */
    public function getAll($key)
    {
        return $this->where('key', '=', $key)
            ->orderBy('name', 'ASC')
            ->get();
    }

    // FUNCTION FOR GET INGREDIENTS
    public function getIngredients()
    {
        return $this->hasMany('App\Ingredient', 'recipe_id', 'id');
    }
}
