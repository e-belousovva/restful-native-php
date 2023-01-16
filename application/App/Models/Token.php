<?php

declare(strict_types=1);

namespace App\Models;

use Exception;

class Token extends Model {

    /**
     * createToken
     *
     * creates a new Token
     *
     * @param array $payload  Contains all the fields that will be created.
     * @return array Anonymous
     */
    public function createToken(array $payload): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("INSERT INTO tokens (user_id, token, expired_at) VALUES (:user_id, :token, :expired_at)");
            $stmt->bindParam('user_id', $payload['user_id']);
            $stmt->bindParam('token', $payload['jwt_token']);
            $stmt->bindParam('expired_at', $payload['expired_at']);
            $stmt->execute();

            return [
                'status' => true,
                'data' => $payload
            ];
        } catch (\PDOException $exception) {
            return [
                'status' => false,
                'data' => []
            ];
        }
    }



    /**
     * fetchToken
     *
     * fetches an existing Token using the $token
     *
     * @param string $token     The token that will be used in matching the closest token from the database.
     * @return array Anonymous
     */
    public function fetchToken(string $token): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("SELECT * FROM tokens WHERE token = :token");
            $stmt->bindParam('token', $token);
            $stmt->execute();

            $data = $stmt->fetch();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!empty($data)) {
            return [
                'status' => true,
                'data' => $data
            ];
        }

        return [
            'status' => false,
            'data' => []
        ];
    }
}