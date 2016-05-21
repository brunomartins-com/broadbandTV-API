<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    protected $table = "nutrient";
    
    // FUNCTION FOR GET NUTRIENTS
    public function getNutrients()
    {
        return $this->hasMany('App\Nutrient', 'ingredient_id', 'id');
    }
}
