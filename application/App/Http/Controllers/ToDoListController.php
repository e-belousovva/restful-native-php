<?php

declare(strict_types=1);


namespace App\Http\Controllers;

use App\Models\Task;
use App\PdfGenerator;
use App\Models\ToDoList;
use Cezpdf;
use Exception;

class ToDoListController extends Controller
{
    public function fetchToDoLists(): void
    {
        try {
            $userId = $_SESSION['user_id'];
            $toDoLists = (new ToDoList())::fetchToDoLists($userId);
            if ($toDoLists['status']) {
                $this->json(200, $toDoLists);
            }
            $this->json(404, ['message' => 'ToDoLists not found']);

        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function fetchToDoListById(int $id)
    {
        try {
            $userId = $_SESSION['user_id'];
            $toDoList = (new ToDoList())::fetchToDoListById($id, $userId);

            if ($toDoList['status']) {
                $this->json(200, $toDoList);
            }

            $this->json(404, ['message' => 'ToDoList not found']);

        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function createNewToDoList()
    {
        $request = $_POST;

        $validationObject = array(
            (object)[
                'validator' => 'required',
                'data' => $request['name'] ?? '',
                'key' => 'ToDoList Name'
            ]
        );

        $validationBag = Parent::validation($validationObject);
        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        try {
            $userId = $_SESSION['user_id'];

            $toDoList = new ToDoList();
            $payload = [
                'name' => $request['name'],
                'user_id' => (int)$userId,
            ];

            $toDoList = $toDoList::createToDoList($payload);

            if ($toDoList['status']) {
                $this->json(200, $toDoList);
            }
        } catch (Exception $e) {
            $this->json($e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function updateToDoList(int $id)
    {
        $request = $this->PUT();

        $validationObject = [
            (object)[
                'validator' => 'required',
                'data' => $request['name'] ?? '',
                'key' => 'ToDoList Name'
            ]
        ];

        $validationBag = Parent::validation($validationObject);
        if ($validationBag['status']) {
            foreach ($validationBag['errors'] as $error) {
                echo $error['message'];
                echo "\n";
            }
            die();
        }

        try {
            $userId = $_SESSION['user_id'];
            $payload = [
                'id' => $id,
                'name' => $request['name'],
                'user_id' => (int)$userId
            ];

            $toDoList = (new ToDoList())::fetchToDoListById($id, $userId);

            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found!']);
            }

            $toDoList = (new ToDoList())::updateToDoList($payload);

            if ($toDoList['status']) {
                $this->json(200, $toDoList);
            }

            $this->json(404, ['message' => 'ToDoList not found']);
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function deleteToDoList(int $id)
    {
        try {
            $userId = $_SESSION['user_id'];

            $toDoList = (new ToDoList())::fetchToDoListById($id, $userId);

            if (!$toDoList['status']) {
                $this->json(404, ['message' => 'ToDoList not found!']);
            }

            $toDoList = (new ToDoList())::deleteToDoList($id);

            if ($toDoList['status']) {
                $this->json(204, []);
            }
            $this->json(404, ['message' => 'ToDoList not found']);
        } catch (Exception $e) {
            $this->json((int)$e->getCode(), ['message' => $e->getMessage()]);
        }
    }

    public function exportToDoList(int $id)
    {
        $userId = $_SESSION['user_id'];

        $toDoList = (new ToDoList())::fetchToDoListById($id, $userId);

        if (!$toDoList['status']) {
            $this->json(404, ['message' => 'ToDoList not found!']);
        }

        $pdf = new Cezpdf('A7','portrait','color',[255,255,255]);
        $pdf->ezSetMargins(20,20,20,20);
        $mainFont = 'Times-Roman';
        $pdf->selectFont($mainFont);
        $pdf->openHere('Fit');
        $toDoListName = $toDoList['data']['name'];
        $pdf->ezText('That\'s my ToDoList', 16, ['justification'=>'centre']);
        $pdf->ezText('<c:color:1,0,0>"'.$toDoListName.'"</c:color>', 16, ['justification'=>'centre']);
        $pdf->ezText('<br>', 14);
        $tasks = (new Task())::fetchTasks($id);
        foreach ($tasks['data'] as $task) {
            $pdf->ezText('- '.$task['name'], 14, ['justification'=>'centre']);
        }

        if (empty($tasks['data'])) {
            $pdf->ezText('<c:color:0,0,1>I haven\'t any tasks yet ;)</c:color>', 14, ['justification'=>'centre']);
        }
        $pdf->ezStream();
    }
}