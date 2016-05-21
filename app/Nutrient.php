<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    // FUNCTION FOR GET NUTRIENTS
    public function getNutrients()
    {
        return $this->hasMany('App\Nutrient', 'ingredient_id', 'id');
    }
}
