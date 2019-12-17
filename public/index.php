<?php

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
        $url="https://api.imgur.com/3/gallery";
        $queryParams = $request->getQueryParams();
        $showViral=isset($queryParams['showViral'])?$queryParams['showViral']:'false';
        $section=isset($args['section'])?$args['section']:'hot';
        $sort=isset($args['sort'])?$args['sort']:'viral';
        $window=isset($args['window'])?$args['window']:'day';
        $page=isset($args['page'])?$args['page']:'0';
        
        $url.=DIRECTORY_SEPARATOR.$section.DIRECTORY_SEPARATOR.$sort.DIRECTORY_SEPARATOR.$window.DIRECTORY_SEPARATOR.$page.'?showViral='.$showViral;

        $client = new Client();
        $dataImgur = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Client-ID ' . 'b35d72c36b0c7f3',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        ]);

        $response->getBody()->write($dataImgur->getBody()->getContents());
        $status = $dataImgur->getStatusCode();

        return $response->withStatus($status)->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
    });
});

$app->run();