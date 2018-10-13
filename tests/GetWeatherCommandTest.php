<?php

namespace Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use App\Command\GetWeatherCommand;
use GuzzleHttp\Client as HttpClient;

class GetWeatherCommandTest extends TestCase
{
    public function testWithSuccessResponse(): void
    {
        $city = 'moscow';
        $config = [
            'baseUrl' => 'https://www.metaweather.com/api/',
            'locationPath' => 'location/search/?query=',
            'weatherPath' => 'location/'
        ];

        $command = new GetWeatherCommand($this->mockGuzzle(), $config);

        $result = $command->execute($city);

        $this->assertEquals(13.34, $result);
    }

    public function mockGuzzle(): HttpClient
    {
        $locationResponse = file_get_contents(__DIR__ . '/__fixtures__/weather.success.location.json');
        $weatherResponse = file_get_contents(__DIR__ . '/__fixtures__/weather.success.weather.json');

        $mock = new MockHandler([
            new Response(200, [], $locationResponse),
            new Response(200, [], $weatherResponse)
        ]);
        $handler = HandlerStack::create($mock);

        return new HttpClient(['handler' => $handler]);
    }
}
