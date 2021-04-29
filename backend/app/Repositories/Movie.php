<?php

namespace App\Repositories;

use App\Services\DB;

class Movie
{
    private $conn;
    private $db;

    public function __construct()
    {
        $this->conn = new DB();
        $this->db = $this->conn->connect();
    }

    public function all(int $userId): ?array
    {
        $sql = 'SELECT * FROM subscriptions WHERE user_id = ? AND deleted_at IS NULL';
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e);

            return null;
        }
    }

    public function exists(int $userId, string $movieId): ?int
    {
        $sql = 'SELECT movie_id FROM subscriptions WHERE user_id = ? AND movie_id = ? AND deleted_at IS NULL';
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $movieId]);
            $existing = count($stmt->fetchAll());
        } catch (\PDOException $e) {
            error_log($e);

            //Not very proud about this solution
            return null;
        }

        return $existing;
    }

    public function save(array $data): ?int
    {
        $sql = 'INSERT INTO subscriptions (user_id, movie_id, movie, rating, poster, year, length, created_at) values(?, ?, ?, ?, ?, ?, ?, ?)';

        try {
            $this->db->prepare($sql)->execute($data);
        } catch (\PDOException $e) {
            error_log($e);

            return null;
        }

        return 1;
    }

    public function update(int $userId, string $movieId): ?int
    {
        $now = date('Y-m-d H:i:s');

        $sql = 'UPDATE subscriptions set deleted_at = ? where user_id = ? and movie_id = ? ';

        try {
            $this->db->prepare($sql)->execute([$now, $userId, $movieId]);
        } catch (\PDOException $e) {
            error_log($e);

            return null;
        }

        return 1;
    }
}
