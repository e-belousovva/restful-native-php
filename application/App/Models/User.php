<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    /**
     * @param array $payload Contains all the fields that will be created.
     * @return array
     */
    public static function createUser(array $payload): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindParam('name', $payload['name']);
            $stmt->bindParam('email', $payload['email']);
            $stmt->bindParam('password', $payload['password']);
            $stmt->execute();

            $payload['user_id'] = parent::$dbConnection->lastInsertId();
            return [
                'status' => true,
                'data' => $payload
            ];
        } catch (\PDOException $exception) {
            return [
                'status' => false,
                'data' => ['message' => $exception->getMessage()]
            ];
        }
    }

    /**
     * @param string $email The email of the row to be fetched...
     * @return array
     */
    public static function checkEmail(string $email): array
    {
        $stmt = parent::$dbConnection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam('email', $email);
        $stmt->execute();

        $user = $stmt->fetch();
        if ($user) {
            return [
                'status' => true,
                'data' => $user
            ];
        }

        return [
            'status' => false,
            'data' => []
        ];
    }
}