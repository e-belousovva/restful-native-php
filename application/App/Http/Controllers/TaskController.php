<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\ToDoList;
use Exception;

class TaskController extends Controller
{
    public function fetchTasks($listId): void
    {
        try {
            $userId = $_SESSION['user_id'];
            $toDoList = (new ToDoList())::fetchToDoListById($listId, $userId);
            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found']);
            }

            $tasks = (new Task())::fetchTasks($listId);

            if ($tasks['status']) {
                $this->json(200, $tasks);
            }

            $this->json(404, ['message' => 'Tasks not found']);
        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function getTaskById(int $listId, int $taskId)
    {
        try {
            $userId = $_SESSION['user_id'];
            $toDoList = (new ToDoList())::fetchToDoListById($listId, $userId);
            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found']);
            }

            $toDoList = (new Task())::fetchTaskById($listId, $taskId);

            if ($toDoList['status']) {
                $this->json(200, [$toDoList]);
            }

            $this->json(404, ['message' => 'Task not found']);
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function createTask(int $listId)
    {
        $request = $_POST;

        $validationObject = array(
            (object)[
                'validator' => 'required',
                'data' => $request['name'] ?? '',
                'key' => 'Task Name'
            ]
        );

        $validationBag = parent::validation($validationObject);
        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        try {
            $userId = $_SESSION['user_id'];

            $toDoList = (new ToDoList())::fetchToDoListById($listId, $userId);
            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found']);
            }

            $payload = [
                'name' => $request['name'],
                'list_id' => $listId,
            ];

            $task = (new Task())::createTask($payload);

            if ($task['status']) {
                $this->json(200, $task);
            }
        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function updateTask(int $listId, int $taskId)
    {
        $request = $this->PUT();

        $validationObject = [
            (object)[
                'validator' => 'required',
                'data' => $request['name'] ?? '',
                'key' => 'Task Name'
            ]
        ];

        $validationBag = parent::validation($validationObject);
        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        try {
            $userId = $_SESSION['user_id'];

            $toDoList = (new ToDoList())::fetchToDoListById($listId, $userId);
            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found']);
            }

            $payload = [
                'id' => $taskId,
                'name' => $request['name'],
                'list_id' => $listId
            ];

            $task = (new Task())::fetchTaskById($listId, $taskId);

            if (!$task['status']) {
                $this->json(404, ['message' => 'Task not found!']);
            }

            $task = (new Task())::updateTask($payload);

            if ($task['status']) {
                $this->json(200, $task);
            }

            $this->json(404, ['message' => 'Task not found']);
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function deleteTask(int $listId, int $taskId)
    {
        try {
            $userId = $_SESSION['user_id'];

            $toDoList = (new ToDoList())::fetchToDoListById($listId, $userId);
            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found']);
            }

            $task = (new Task())::fetchTaskById($listId, $taskId);

            if (!$task['status']) {
                $this->json(404, ['message' => 'Task not found!']);
            }

            $task = (new Task())::deleteTask($taskId);

            if ($task['status']) {
                $this->json(204, []);
            }
            $this->json(404, ['message' => 'Task not found']);
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }
}