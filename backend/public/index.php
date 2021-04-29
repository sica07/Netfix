<?php

use App\Services\Movies;
use App\Services\Response as Resp;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

require __DIR__.'/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, false, false);

//ENABLE CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
//END OF ENABLE CORS

$app->get('/movies', function (Request $request, Response $response, $args) {
    $movies = new Movies();
    $payload = $movies->get();

    return Resp::success($response, $payload);
});

$app->post('/{userid}/{imdbid}', function (Request $request, Response $response, $args) {
    $repo = new \App\Repositories\Movie();

    $existing = $repo->exists($args['userid'], $args['imdbid']);

    if (null === $existing) {
        return Resp::error($response);
    }

    if ($existing > 0) {
        return Resp::error($response, 'A subscription for this show already exists!');
    }

    $movie = json_decode($request->getBody()->getContents());

    if ($invalid = (new \App\Validators\MovieValidator($movie))->isInvalid()) {
        return Resp::error($response, $invalid);
    }

    $now = date('Y-m-d H:i:s');

    $data = [
        $args['userid'],
        $args['imdbid'],
        $movie->title,
        $movie->rating,
        $movie->poster,
        $movie->year,
        $movie->length,
        $now,
    ];

    $result = $repo->save($data);

    if (null === $result) {
        return Resp::error($response);
    }

    return Resp::success($response);
});
$app->delete('/{userid}/{imdbid}', function (Request $request, Response $response, $args) {
    $repo = new \App\Repositories\Movie();

    $result = $repo->update($args['userid'], $args['imdbid']);

    if (null === $result) {
        return Resp::error($response);
    }

    return Resp::success($response);
});

$app->get('/{userid}', function (Request $request, Response $response, $args) {
    $repo = new \App\Repositories\Movie();
    $result = $repo->all($args['userid']);

    if (null === $result) {
        return Resp::error($response);
    }

    return Resp::success($response, ['success' => 1, 'data' => $result]);
});

/*
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});
$app->run();
