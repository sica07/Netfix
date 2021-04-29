<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface as Resp;

class Response
{
    public static function success(Resp $response, array $success = null): Resp
    {
        $success = ($success) ? $success : ['success' => 1];
        $response->getBody()->write(json_encode($success));

        return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public static function error(Resp $response, string $error = null): Resp
    {
        $error = ($error) ? $error : 'We are sorry but an error occured. Please try again!';

        $response->getBody()->write(json_encode(['error' => $error]));

        return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
