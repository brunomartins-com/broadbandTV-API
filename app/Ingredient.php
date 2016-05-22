<?php

namespace App;

use App\Libraries\Usda;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = "ingredient";
    private $usda = '';

    /**
     * @param string $usda
     */
    public function setUsda(Usda $usda)
    {
        $this->usda = $usda;
    }

    public function exists($ndbno)
    {
        if (empty($this->usda))
        {
            return false;
        }
        $ingredientInfo = $this->usda->getIngredientInfo($ndbno);
        $arrIngredientInfo = json_decode($ingredientInfo, true);
        
        return key_exists('report', $arrIngredientInfo);
    }

    // FUNCTION FOR GET RECIPE
    public function getRecipe()
    {
        return $this->belongsTo('App\Recipe');
    }
}
