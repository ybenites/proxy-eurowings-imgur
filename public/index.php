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


$app->run();