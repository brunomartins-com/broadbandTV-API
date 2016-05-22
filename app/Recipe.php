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
    
    public function getRecipe($key, $id)
    {
        $usda = New Usda();
        $strSQL = 'select 
                        r.name as recipe_name,
                        i.name as ingredient_name,
                        i.id as ingredient_id,
                        i.ndbno,
                        i.quantity,
                        i.unit,
                        n.nutrient_id
                    from recipe r 
                    join ingredient i on i.recipe_id = r.id
                    join nutrient n on n.ingredient_id = i.id
                    where 
	                  r.id = :id';

        $rec = DB::select($strSQL, ['id' => $id]);
        $recipe = [];
        $i = 1;
        foreach ($rec as $fullRecipe)
        {
            $recipe['name'] = $fullRecipe->recipe_name;
            //if ($i == 1)
                $recipe['ingredients'][$fullRecipe->ingredient_id] = $usda->getIngredientInfo($fullRecipe->ndbno);

            $i++;
            //dd($recipe);
            //$nutrients[] = $fullRecipe->nutrient_id;
        }
    dd($recipe);
        //$usda->getNutritionInfo($nutrients);
    }

    // FUNCTION FOR GET INGREDIENTS
    public function getIngredients()
    {
        return $this->hasMany('App\Ingredient', 'recipe_id', 'id');
    }
}
