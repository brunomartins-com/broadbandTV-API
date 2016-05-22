<?php

namespace App\Http\Controllers;

use App\Libraries\Errors;
use App\Libraries\Usda;
use App\Nutrient;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Ingredient;
use Illuminate\Http\Request;

use App\Http\Requests;

class IngredientController extends Controller
{
    private $ingredient;
    private $nutrient;

    public function __construct(Ingredient $ingredient, Nutrient $nutrient)
    {
        $this->ingredient = $ingredient;
        $this->nutrient = $nutrient;
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
            'ndbno' => "required|max:5",
            'quantity' => 'required|regex:/^\d*(\.\d{0,2})?$/', 
            'unit' => 'required|max:145',
        ], [
            'exists' => 'The selected Recipe does not exist!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $response = [
            'status' => false,
            'message' => 'Ingredient does not exist!',
            'id' => 0
        ];

        $usda = new Usda();
        $arrIngredient = $usda->getIngredientInfo($request->ndbno);

        if (is_array($arrIngredient)) {

            $ingredient = new $this->ingredient;
            $ingredient->name = $arrIngredient['report']['food']['name'];
            $ingredient->recipe_id = $request->recipeId;
            $ingredient->ndbno = $request->ndbno;
            $ingredient->quantity = $request->quantity;
            $ingredient->unit = $request->unit;
            $ingredient->save();

            foreach ($arrIngredient['report']['food']['nutrients'] as $nutrientInfo)
            {
                $nutrient[] = [
                    'nutrient_id' => $nutrientInfo['nutrient_id'],
                    'ingredient_id' => $ingredient->id
                ];
            }

            $this->nutrient->insert($nutrient);

            $response = [
                'status' => true,
                'message' => 'Ingredient added successfully!',
                'id' => $ingredient->id
            ];
        }

        return json_encode($response);
    }

    /**
     * Edit an ingredient
     *
     * @param Request
     *
     * @return json
     */
    public function putEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:ingredient,id',
            'quantity' => 'required|regex:/^\d*(\.\d{0,2})?$/',
            'unit' => 'required|max:145',
        ], [
            'exists' => 'The selected Ingredient does not exist in any recipe!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $ingredient = $this->ingredient->find($request->id);
        $ingredient->quantity   = $request->quantity;
        $ingredient->unit       = $request->unit;
        $ingredient->save();

        $response = ['status' => true, 'message' => 'Recipe edited successfully!'];

        return json_encode($response);
    }

    /**
     * Delete recipe's ingredient
     *
     * @param Request
     *
     * @return json
     */
    public function delete(Request $request)
    {
        $response = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:ingredient,id',
        ], [
            'exists' => 'Selected Ingredient does not exist in any recipe to be deleted!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        try {
            $this->ingredient
                ->where('id', '=', $request->id)
                ->delete();
        }catch (QueryException $e){
            $response = ['status' => false, 'message' => 'An error occurred while deleting.'];
            return json_encode($response);
        }

        $response = ['status' => true, 'message' => 'Ingredient was deleted successfully!'];
        return json_encode($response);
    }
}
