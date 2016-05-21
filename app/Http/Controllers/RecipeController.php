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
        $name           = $request->name;
        $key            = $request->key;
        $allowDuplicity = (bool) $request->allowDuplicity;

        $response = [];
        if(!$allowDuplicity)
        {
            if($this->recipe->exists($name, $key))
            {
                $response = ['status' => false, 'message' => 'The recipe '.$name.' already exist in database.'];
                return json_encode($response);
            }
        }
        $recipe = new $this->recipe;
        $recipe->name   = $name;
        $recipe->key    = $key;
        $recipe->save();

        $response = [];

        return json_encode($response);
    }
}
