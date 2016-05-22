<?php

namespace App;

use App\Libraries\Usda;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = "ingredient";

    // FUNCTION FOR GET RECIPE
    public function getRecipe()
    {
        return $this->belongsTo('App\Recipe');
    }
}
