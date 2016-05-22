<?php

namespace App\Http\Controllers;

use App\Libraries\Errors;
use App\Libraries\Usda;
use App\Log;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Ingredient;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Requests;

class IngredientController extends Controller
{
    private $ingredient;
    private $log;

    public function __construct(Ingredient $ingredient, Log $log)
    {
        $this->ingredient   = $ingredient;
        $this->log          = $log;
    }

    public function getList(Request $request)
    {
        $type = isset($request->type) ? $request->type : "b";

        $all_request = $request->all();
        if(array_has($all_request, 'type')) {
            unset($all_request['type']);
        }
        if(count($all_request) <= 1) {
            return json_encode(['status' => false, 'message' => 'At least one information is required.']);
        }

        $ingredients = $this->ingredient->orderBy('name', 'ASC');

        if($request->has('name')) {
            $ingredients = $ingredients->where('name', 'like', '%'.$request->name.'%');
        }
        if($request->has('ndbno')) {
            $ingredients = $ingredients->where('ndbno', '=', $request->ndbno);
        }
        if($request->has('id')) {
            $ingredients = $ingredients->where('id', '=', $request->id);
        }

        $ingredients = $ingredients->get();

        if(count($ingredients) == 0) {
            return json_encode(['status' => true, 'message' => 'You do not have ingredients for your recipe yet.']);
        }

        if($type == 'f') {
            foreach ($ingredients as $ingredient) {
                array_set($ingredient, 'get_recipe', $ingredient->getRecipe);
            }
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
        $response = [];
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|integer|exists:recipe,id',
            'ndbno'     => "required|max:5",
            'quantity'  => 'required|regex:/^\d*(\.\d{0,2})?$/',
            'unit'      => 'required|max:145',
        ], [
            'exists'    => 'The selected Recipe does not exist!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $response = [
            'status'    => false,
            'message'   => 'Ingredient does not exist!',
            'id'        => 0
        ];

        $usda = new Usda();
        $arrIngredient = $usda->getIngredientInfo($request->ndbno);

        if (is_array($arrIngredient)) {

            $ingredient = new $this->ingredient;
            $ingredient->name = $arrIngredient['report']['food']['name'];
            $ingredient->recipe_id = $request->recipe_id;
            $ingredient->ndbno = $request->ndbno;
            $ingredient->quantity = $request->quantity;
            $ingredient->unit = $request->unit;
            $ingredient->save();

            $response = [
                'status'    => true,
                'message'   => 'Ingredient added successfully!',
                'id'        => $ingredient->id
            ];
        }

        // Record log
        $this->log->logAdd($response['message'], $request->key, __CLASS__, __METHOD__, $ingredient->id);

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
        $response = [];
        $validator = Validator::make($request->all(), [
            'id'        => 'required|integer|exists:ingredient,id',
            'quantity'  => 'required|regex:/^\d*(\.\d{0,2})?$/',
            'unit'      => 'required|max:145',
        ], [
            'exists'    => 'The selected Ingredient does not exist in any recipe!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $ingredient             = $this->ingredient->find($request->id);
        $ingredient->quantity   = $request->quantity;
        $ingredient->unit       = $request->unit;
        $ingredient->save();

        $response = [
            'status'    => true,
            'message'   => 'Ingredient edited successfully!'
        ];

        // Record log
        $this->log->logAdd($response['message'], $request->key, __CLASS__, __METHOD__, $request->id);

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
            'id'        => 'required|integer|exists:ingredient,id',
        ], [
            'exists'    => 'Selected Ingredient does not exist in any recipe to be deleted!'
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

        $response = [
            'status'    => true,
            'message'   => 'Ingredient was deleted successfully!'
        ];

        // Record log
        $this->log->logAdd($response['message'], $request->key, __CLASS__, __METHOD__, $request->id);

        return json_encode($response);
    }
}
