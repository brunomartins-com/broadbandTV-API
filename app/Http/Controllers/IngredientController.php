<?php

namespace App\Http\Controllers;

use App\Libraries\Usda;
use Validator;
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

    /**
     * Add a new recipe for user
     *
     * @param Request
     *
     * @return json
     */
    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:145',
            'recipeId' => 'required|integer|exists:recipe,id',
            'ndbno' => 'required|max:5',
            'quantity' => 'required|max:145', //TODO: Change to decimal (maybe using regex)
            'unit' => 'required|max:145',
        ], [
            'exists' => 'The selected Recipe does not exist!'
        ]);

        if ($validator->fails()) {

            //Gettins error messages
            foreach ($validator->messages()->all() as $message)
            {
                $errors[] = $message;
            }

            $response = [
                'status' => false,
                'message' => 'Please, check the listed erros.',
                'erros' => $errors
            ];
            return json_encode($response);
        }

        $response = [
            'status' => false,
            'message' => 'Ingredient does not exist!',
            'id' => 0
        ];

        $this->ingredient->setUsda(new Usda());
        if ($this->ingredient->exists($request->ndbno)) {

            $ingredient = new $this->ingredient;
            $ingredient->name = $request->name;
            $ingredient->recipe_id = $request->recipeId;
            $ingredient->ndbno = $request->ndbno;
            $ingredient->quantity = $request->quantity;
            $ingredient->unit = $request->unit;
            $ingredient->save();

            $response = [
                'status' => true,
                'message' => 'Ingredient added successfully!',
                'id' => $ingredient->id
            ];
        }

        return json_encode($response);
    }
}
