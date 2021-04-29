<?php

error_reporting(E_ALL);
ini_set('ignore_repeated_errors', true);
ini_set('display_errors', false);
ini_set('log_errors', true);
ini_set('error_log', config('error_file'));

function config($param)
{
    $config = [
    'db' => [
      'path' => 'db/netfix.db',
    ],
    'error_file' => __DIR__.'/../storage/error.log',
    'api_key' => '777cUbw0iNmshzL3lJrsFhkRlXuxp1NpVhhjsnGaj6Qu6tfwQh',
  ];

    if (isset($config[$param])) {
        return $config[$param];
    }

    return;
}
