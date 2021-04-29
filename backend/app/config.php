<?php
function config($param) {
  $config = [
    'db' => [
      'path' => 'db/netfix.db',
    ],
    'error_file' => __DIR__ . '/../storage/error.log',
    'api_key' => '777cUbw0iNmshzL3lJrsFhkRlXuxp1NpVhhjsnGaj6Qu6tfwQh'
  ];

  if(isset($config[$param])) {
    return $config[$param];
  }
  return;
}
