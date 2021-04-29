<?php

namespace App\Services;

class DB {
  private $pdo;

  public function connect()
  {
    if($this->pdo == null) {
      $path = __DIR__ . '/../../' . config('db')['path'];
      $this->pdo = new \PDO('sqlite:' . $path);
    }

    $this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

    return $this->pdo;
  }

}
