<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Database\QueryException;
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

    public function getList(Request $request)
    {
        $recipes = $this->recipe
            ->where('key', '=', $request->key)
            ->orderBy('name', 'ASC')
            ->get();

        if(count($recipes) == 0)
        {
            return json_encode(['status' => true, 'message' => 'You do not have recipe yet.']);
        }

        return json_encode($recipes);
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:145',
        ]);

        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'The name is required.'];
            return json_encode($response);
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

        $response = ['status' => true, 'message' => 'Recipe added successfully!', 'id' => $recipe->id];

        return json_encode($response);
    }

    public function putEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:145',
        ]);

        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'The name is required.'];
            return json_encode($response);
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

        $response = ['status' => true, 'message' => 'Recipe edited successfully!'];

        return json_encode($response);
    }
    
    public function delete(Request $request)
    {
        $id             = $request->id;
        $key            = $request->key;

        $response = [];

        try {
            $this->recipe
                ->where('id', '=', $id)
                ->where('key', '=', $key)
                ->delete();
        }catch (QueryException $e){
            $response = ['status' => false, 'message' => 'An error occurred while deleting.'];
            return json_encode($response);
        }

        $response = ['status' => true, 'message' => 'Recipe deleted successfully!'];
        return json_encode($response);
    }
}
