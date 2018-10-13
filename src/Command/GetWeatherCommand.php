<?php

namespace App\Command;

use GuzzleHttp\ClientInterface;

class GetWeatherCommand
{
    private $httpClient;
    private $config;

    public function __construct(ClientInterface $httpClient, array $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    public function execute(string $city): float
    {
       $cityId = $this->getCityId($city);

       try {
            $result = $this->getWeather($cityId);
       } catch (\Exception $e) {
           // bad path
           echo $e->getMessage();
           return '';
       }

       return $result;
    }

    private function getCityId(string $city)
    {
        $locationPath = $this->config['baseUrl'] . $this->config['locationPath'];

        $res = $this->httpClient->request('GET', $locationPath, [
            'query' => ['query' => $city],
        ]);

        //parse response
        $parsed = $res->getBody()->getContents();
        $arr = json_decode($parsed, JSON_OBJECT_AS_ARRAY);
        return  $arr[0]['woeid'];
    }

    private function getWeather($cityId)
    {
        $weatherPath = $this->config['baseUrl'] . $this->config['weatherPath'] . $cityId;
        $res = $this->httpClient->request('GET', $weatherPath);

        $parsed = $res->getBody()->getContents();
        $arr = json_decode($parsed, JSON_OBJECT_AS_ARRAY);

        return $arr['consolidated_weather'][0]['the_temp'];
    }
}
