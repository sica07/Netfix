<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use App\Services\DB;
use App\Services\Movies;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set("log_errors", TRUE);
ini_set('error_log', config('error_file'));

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
    $movies = new Movies;
    $payload = $movies->get();

    $response->getBody()->write($payload);
    return $response
          ->withHeader('Content-Type', 'application/json');
});

$app->post('/{userid}/{imdbid}', function (Request $request, Response $response, $args) {
    $movie = json_decode($request->getBody()->getContents());

    $now = date('Y-m-d H:i:s');

    $conn = new DB;
    $db = $conn->connect();

    $sql = 'SELECT movie_id FROM subscriptions WHERE user_id = ? AND movie_id = ? AND deleted_at IS NULL';

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([$args['userid'], $args['imdbid']]);
        $existing = count($stmt->fetchAll());
    } catch(\PDOException $e) {
        error_log($e);
        $response->getBody()->write(json_encode(['error' => 'An error occured']));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    if($existing > 0) {
        $response->getBody()->write(json_encode(['error' => 'A subscription for this show already exists!']));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    $sql = 'INSERT INTO subscriptions (user_id, movie_id, movie, rating, poster, year, length, created_at) values(?, ?, ?, ?, ?, ?, ?, ?)';

    $data = [
        $args['userid'],
        $args['imdbid'],
        $movie->title,
        $movie->rating,
        $movie->poster,
        $movie->year,
        $movie->length,
        $now
    ];

    try {
        $db->prepare($sql)->execute($data);
    } catch(\PDOException $e) {
        var_dump($e->getMessage());
        error_log($e);
        $response->getBody()->write(json_encode(['error' => 'An error occured']));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['success' => 1]));

    return $response
          ->withHeader('Content-Type', 'application/json');

});

$app->delete('/{userid}/{imdbid}', function (Request $request, Response $response, $args) {
    $now = date('Y-m-d H:i:s');

    $conn = new DB;
    $db = $conn->connect();

    $sql = 'UPDATE subscriptions set deleted_at = ? where user_id = ? and movie_id = ? ';

    try {
        $db->prepare($sql)->execute([$now, $args['userid'], $args['imdbid']]);
    } catch(\PDOException $e) {
        error_log($e);
        $response->getBody()->write(json_encode(['error' => 'An error occured']));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode(['success' => 1]));

    return $response
          ->withHeader('Content-Type', 'application/json');

});

$app->get('/{userid}', function (Request $request, Response $response, $args) {

    $conn = new DB;
    $db = $conn->connect();

    $sql = 'SELECT * FROM subscriptions WHERE user_id = ? AND deleted_at IS NULL';

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([$args['userid']]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        var_dump($e->getMessage());
        $response->getBody()->write(json_encode(['error' => 'An error occured']));
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    $response->getBody()->write(json_encode(['success' => 1, 'data' => $result]));

    return $response
          ->withHeader('Content-Type', 'application/json');

});

/**
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});
$app->run();
