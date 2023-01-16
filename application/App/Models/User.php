<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{

    /**
     * createUser
     *
     * creates a new User
     *
     * @param array $payload Contains all the fields that will be created.
     * @return array Anonymos
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
     * fetchUserById
     *
     * fetches a user by it's Id
     *
     * @param $id
     * @return array Anonymous
     */
    public static function fetchUserById($id): array
    {
        $Sql = "SELECT id, firstName, lastName, email, created_at, updated_at FROM `users` WHERE id = :id";
        Parent::query($Sql);
        // Bind Params...
        Parent::bindParams('id', $id);
        $Data = Parent::fetch();

        if (!empty($Data)) {
            return [
                'status' => true,
                'data' => $Data
            ];
        }

        return [
            'status' => false,
            'data' => []
        ];
    }

    /**
     * checkEmail
     *
     * fetches a user by it's email
     *
     * @param string $email The email of the row to be fetched...
     * @return array Anonymos
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