<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use PDO;

class ToDoList extends Model
{
    public static function fetchToDoLists(int $userId)
    {
        try {
            $stmt = parent::$dbConnection->prepare("SELECT * FROM to_do_lists WHERE user_id = :userId");
            $stmt->bindParam('userId', $userId);
            $stmt->execute();

            $toDoLists = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        if (empty($toDoLists)) {
            return [
                'status' => false,
                'data' => []
            ];
        }

        return [
            'status' => true,
            'data' => $toDoLists
        ];
    }

    public static function fetchToDoListById(int $id, $userId): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("SELECT * FROM to_do_lists WHERE user_id = :userId AND id = :id");
            $stmt->bindParam('userId', $userId);
            $stmt->bindParam('id', $id);
            $stmt->execute();

            $toDoList = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        if (empty($toDoList)) {
            return [
                'status' => false,
                'data' => []
            ];
        }

        return [
            'status' => true,
            'data' => $toDoList
        ];
    }

    public static function createToDoList($payload)
    {
        try {
            $stmt = parent::$dbConnection->prepare("INSERT INTO to_do_lists (name, user_id) VALUES (:name, :user_id)");
            $stmt->bindParam('name', $payload['name']);
            $stmt->bindParam('user_id', $payload['user_id']);
            $stmt->execute();

            return [
                'status' => true,
                'data' => $payload
            ];
        } catch (\PDOException $exception) {
            http_response_code((int)$exception->getCode());
            header('Content-Type: application/json; charset=utf-8');

            echo $exception->getMessage();
        }
    }

    public static function updateToDoList($payload)
    {
        try {
            $stmt = parent::$dbConnection->prepare("UPDATE to_do_lists SET name = :name WHERE id = :id and user_id = :user_id");
            $stmt->bindParam('name', $payload['name']);
            $stmt->bindParam('id', $payload['id']);
            $stmt->bindParam('user_id', $payload['user_id']);
            $stmt->execute();

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

    public static function deleteToDoList(int $id): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("DELETE FROM to_do_lists WHERE id = :id");
            $stmt->bindParam('id', $id);
            $stmt->execute();

            return [
                'status' => true,
                'data' => []
            ];
        } catch (\PDOException $exception) {
            return [
                'status' => false,
                'data' => ['message' => $exception->getMessage()]
            ];
        }
    }
}
