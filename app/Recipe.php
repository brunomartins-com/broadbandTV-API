<?php

namespace App;

use App\Libraries\Usda;
use Faker\UniqueGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get Recipe Full Information
     *
     * @param Recipe ID
     * 
     * @return array
     */
    public function getRecipe($id)
    {
        $usda = New Usda();
        $strSQL = 'select 
                        r.name as recipe_name,
                        i.name as ingredient_name,
                        i.id as ingredient_id,
                        i.ndbno,
                        i.quantity,
                        i.unit
                    from recipe r 
                    join ingredient i on i.recipe_id = r.id
                    where 
	                  r.id = :id';

        $rec = DB::select($strSQL, ['id' => $id]);
        $recipe = [];
        foreach ($rec as $fullRecipe)
        {
            $recipe['name'] = $fullRecipe->recipe_name;
            $recipe['ingredients'][$fullRecipe->ingredient_id] = $usda->getIngredientInfo($fullRecipe->ndbno);
        }

        return $recipe;
    }
}
