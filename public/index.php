<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

/*$container = $app->getContainer();

$container->set('SubscriptionController', function (ContainerInterface $container) {
    return new \App\Controllers\SubscriptionController();
});

$app->get('/all', \SubscriptionController::class . ':index');*/

$app->get('/', function (Request $request, Response $response, $args) {
    $data = array('name' => 'Bob', 'age' => 40);
    $payload = json_encode($data);

    $response->getBody()->write($payload);

    return $response
          ->withHeader('Content-Type', 'application/json');
});

$app->run();
