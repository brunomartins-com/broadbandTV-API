<?php

namespace App\Libraries;


use Illuminate\Support\Facades\URL;

class Usda
{
    private $reportURL = 'http://api.nal.usda.gov/ndb/reports/?';
    private $nutritionURL = 'http://api.nal.usda.gov/ndb/nutrients/?';
    private $type = 'b';
    private $format = 'json';
    private $apiKey = '';

    public function __construct()
    {
        $this->apiKey = env('USDA_KEY');
    }

    /**
     * @return string
     */
    public function getReportURL()
    {
        return $this->reportURL;
    }

    /**
     * @param string $reportURL
     */
    public function setReportURL($reportURL)
    {
        $this->reportURL = $reportURL;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getNutritionURL()
    {
        return $this->nutritionURL;
    }

    /**
     * @param string $nutritionURL
     */
    public function setNutritionURL($nutritionURL)
    {
        $this->nutritionURL = $nutritionURL;
    }

    /**
     * Get all ingredient information
     *
     * @param USDA ID of ingredient
     *
     * @return json|false
     */
    public function getIngredientInfo($ingredientUSDAID)
    {
        $params = ['ndbno' => $ingredientUSDAID,
            'type' =>$this->type,
            'format' => $this->format,
            'api_key' => $this->apiKey
        ];
        $url = $this->reportURL.http_build_query($params);
        $ingredientInfo = json_decode($this->execCURL($url), true);

        if (key_exists('errors', $ingredientInfo))
        {
            return false;
        }

        return $ingredientInfo;
    }

    /**
     * Exec cRUL
     *
     * @param Full URL
     *
     * @return String
     */
    private function execCURL($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }
}