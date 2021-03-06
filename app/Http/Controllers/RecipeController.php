<?php

namespace App\Http\Controllers;

use App\Ingredient;
use App\Libraries\Errors;
use App\Log;
use Validator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Recipe;
use App\Http\Requests;

class RecipeController extends Controller
{
    private $recipe;
    private $ingredient;
    private $log;

    public function __construct(Recipe $recipe, Ingredient $ingredient, Log $log)
    {
        $this->recipe       = $recipe;
        $this->ingredient   = $ingredient;
        $this->log          = $log;
    }

    /**
     * Get all recipes from a user
     *
     * @param Request
     *
     * @return json
     */
    public function getList(Request $request)
    {
        $recipes = $this->recipe->getAll($request->key);

        if(count($recipes) == 0) {
            return json_encode(['status' => true, 'message' => 'You do not have recipe yet.']);
        }

        return json_encode($recipes);
    }

    /**
     * Get Full Report about Recipe
     *
     * @param Request
     *
     * @return json
     */
    public function getRecipe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $recipeInfo = $this->recipe->getRecipe($request->id);

        if (count($recipeInfo) == 0)
        {
            return json_encode(['status' => false, 'message' => 'Recipe not found.']);
        }

        return json_encode(['status' => true, 'recipeInfo' => $recipeInfo]);
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
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

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

        $response = [
            'status'    => true,
            'message'   => 'Recipe added successfully!',
            'id'        => $recipe->id
        ];

        // Record log
        $this->log->logAdd($response['message'], $key, __CLASS__, __METHOD__, $recipe->id);

        return json_encode($response);
    }

    /**
     * Edit a recipe
     *
     * @param Request
     *
     * @return json
     */
    public function putEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required|integer',
            'name'  => 'required|max:145',
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        $id             = $request->id;
        $name           = $request->name;
        $key            = $request->key;
        $allowDuplicity = (bool) $request->allowDuplicity;

        $response = [];
        if(!$allowDuplicity)
        {
            if($this->recipe->exists($name, $key, $id))
            {
                $response = ['status' => false, 'message' => 'The recipe '.$name.' already exist in database.'];
                return json_encode($response);
            }
        }
        $recipe = $this->recipe->find($id);
        $recipe->name   = $name;
        $recipe->save();

        $response = [
            'status'    => true,
            'message'   => 'Recipe was edited successfully!'
        ];

        // Record log
        $this->log->logAdd($response['message'], $key, __CLASS__, __METHOD__, $id);

        return json_encode($response);
    }

    /**
     * Delete user's recipe
     *
     * @param Request
     *
     * @return json
     */
    public function delete(Request $request)
    {
        $response = [];

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:recipe,id',
        ], [
            'exists' => 'Selected Recipe does not exist to be deleted!'
        ]);

        if ($validator->fails()) {
            $errors = new Errors();
            return $errors->getAsJSON($validator);
        }

        try {
            $this->ingredient
                ->where('recipe_id', '=', $request->id)
                ->delete();
            
            $this->recipe
                ->where('id', '=', $request->id)
                ->where('key', '=', $request->key)
                ->delete();
        }catch (QueryException $e){
            $response = ['status' => false, 'message' => 'An error occurred while deleting.'];
            return json_encode($response);
        }

        $response = [
            'status'    => true,
            'message'   => 'Recipe was deleted successfully!'
        ];

        // Record log
        $this->log->logAdd($response['message'], $request->key, __CLASS__, __METHOD__, $request->id);

        return json_encode($response);
    }
}
