<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Task extends Model
{
    public static function fetchTasks($listId): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("SELECT * FROM tasks WHERE list_id = :listId");
            $stmt->bindParam('listId', $listId);
            $stmt->execute();

            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'status' => true,
                'data' => $tasks
            ];
        } catch (\PDOException $e) {
            return [
                'status' => false,
                'data' => []
            ];
        }
    }

    public static function fetchTaskById(int $listId, int $taskId): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("SELECT * FROM tasks WHERE list_id = :listId and id = :id");
            $stmt->bindParam('listId', $listId);
            $stmt->bindParam('id', $taskId);
            $stmt->execute();

            $task = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {

        }

        if (empty($task)) {
            return [
                'status' => false,
                'data' => []
            ];
        }

        return [
            'status' => true,
            'data' => $task
        ];
    }

    /**
     * @param array $payload  Contains all the fields that will be created.
     * @return array
     */
    public static function createTask(array $payload): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("INSERT INTO tasks (name, list_id) VALUES (:name, :list_id)");
            $stmt->bindParam('name', $payload['name']);
            $stmt->bindParam('list_id', $payload['list_id']);
            $stmt->execute();

            return [
                'status' => true,
                'data' => $payload
            ];
        } catch (\PDOException $exception) {
            return [
                'status' => false,
                'data' => $exception->getMessage()
            ];
        }
    }

    /**
     * @param $payload
     * @return array
     */
    public static function updateTask($payload): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("UPDATE tasks SET name = :name WHERE id = :id and list_id = :list_id");
            $stmt->bindParam('name', $payload['name']);
            $stmt->bindParam('id', $payload['id']);
            $stmt->bindParam('list_id', $payload['list_id']);
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

    /**
     * @param int $id
     * @return array
     */
    public static function deleteTask(int $id): array
    {
        try {
            $stmt = parent::$dbConnection->prepare("DELETE FROM tasks WHERE id = :id");
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
