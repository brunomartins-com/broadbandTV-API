<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Recipe;
use App\Http\Requests;

class RecipeController extends Controller
{
    private $recipe;

    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    public function getRecipes()
    {
        $recipes = $this->recipe
            ->orderBy('name', 'ASC')
            ->get();

        return json_encode($recipes);
    }

    public function postAdd(Request $request)
    {
        $name = $request->name;
        $allowDuplicity = (bool) $request->allowDuplicity;

        $response = [];
        if(!$allowDuplicity)
        {
            $recipeCount = $this->recipe->where('user', )->where('name', '=', $name)->count();
            if($recipeCount > 0)
            {
                $response = ['status' => false, 'message' => 'The recipe '.$name.' already exist in database.'];
                return json_encode($response);
            }
        }
        $recipe = new $this->recipe;
        $recipe->name = $name;
        $recipe->save();

        $response = [];

        return json_encode($response);
    }
}
