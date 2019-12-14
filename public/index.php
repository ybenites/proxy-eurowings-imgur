<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(-1);

use GuzzleHttp\Client;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->setBasePath("/proxy-eurowings-imgur");


$app->addRoutingMiddleware();
// $app->addErrorMiddleware(false, true, true);
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("welcome");
    return $response;
});

$routeCollector = $app->getRouteCollector();
$routeCollector->setCacheFile(__DIR__.'/../CachePages/cache.file');

$app->group('/api/v1', function ($app) {
    $app->get('/{section}/{sort}/{window}/{page}', function (Request $request, Response $response, $args) {

        $client = new Client();
        $dataImgur = $client->request('GET', 'https://api.imgur.com/3/gallery/hot/viral/day/1', [
            'headers' => [
                'Authorization' => 'Client-ID ' . 'b35d72c36b0c7f3',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        $response->getBody()->write($dataImgur->getBody()->getContents());
        $status = $dataImgur->getStatusCode();

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    });
});

$app->run();