<?php

namespace App\Http\Controllers;

use App\Ingredient;
use Illuminate\Http\Request;

use App\Http\Requests;

class IngredientController extends Controller
{
    private $ingredient;

    public function __construct(Ingredient $ingredient)
    {
        $this->ingredient = $ingredient;
    }

    public function getList(Request $request)
    {
        $ingredients = $this->ingredient
            ->where('recipe_id', '=', $request->recipe_id)
            ->orderBy('name', 'ASC')
            ->get();

        if(count($ingredients) == 0)
        {
            return json_encode(['status' => true, 'message' => 'You do not have ingredients for your recipe yet.']);
        }

        return json_encode($ingredients);
    }
}
