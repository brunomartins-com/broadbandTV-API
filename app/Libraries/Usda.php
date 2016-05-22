<?php

namespace App\Libraries;


use Illuminate\Support\Facades\URL;

class Usda
{
    private $reportURL = 'http://api.nal.usda.gov/ndb/reports/?';
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
     * Get all ingredient information
     *
     * @param USDA ID of ingredient
     *
     * @return json
     */
    public function getIngredientInfo($ingredientUSDAID)
    {
        $params = ['ndbno' => $ingredientUSDAID,
            'type' =>$this->type,
            'format' => $this->format,
            'api_key' => $this->apiKey
        ];
        $url = $this->reportURL.http_build_query($params);
        return $this->execCURL($url);
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        curl_close($ch);

        return $server_output;
    }
}