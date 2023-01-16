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
    /**
     * createProduct
     *
     * Creates a new Product.
     *
     * @param mixed $request $response Contains the Request and Respons Object from the router.
     * @return mixed Anonymous
     */
    public function createProduct($request, $response)
    {
        $Response = [];
        // Call the JSON Middleware
        $FormDataMiddleware = new RequestMiddleware();
        $formData = $FormDataMiddleware::acceptsFormData();

        if (!$formData) {
            $Response[] = [
                'status' => 400,
                'message' => 'Sorry, Only Multipart Form Data Contents are allowed to access this Endpoint.',
                'data' => []
            ];

            $response->code(400)->json($Response);
            return;
        }

        $JwtMiddleware = new JwtMiddleware();
        $jwtMiddleware = $JwtMiddleware::getAndDecodeToken();
        if (isset($jwtMiddleware) && $jwtMiddleware == false) {
            $response->code(400)->json([
                'status' => 401,
                'message' => 'Sorry, the authenticity of this token could not be verified.',
                'data' => []
            ]);
            return;
        }

        $Data = $request->paramsPost();
        $validationObject = array(
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->name) ? $Data->name : '',
                'key' => 'Product Name'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->catalog_id) ? $Data->catalog_id : '',
                'key' => 'Product Catalog'
            ],
            (Object) [
                'validator' => 'toDoListExists',
                'data' => isset($Data->catalog_id) ? $Data->catalog_id : '',
                'key' => 'Product Catalog'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->price) ? $Data->price : '',
                'key' => 'Product Price'
            ],
            (Object) [
                'validator' => 'numeric',
                'data' => isset($Data->price) ? $Data->price : '',
                'key' => 'Product Price'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->color) ? $Data->color : '',
                'key' => 'Product Color'
            ],
            (Object) [
                'validator' => 'string',
                'data' => isset($Data->color) ? $Data->color : '',
                'key' => 'Product Color'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->size) ? $Data->size : '',
                'key' => 'Product Size'
            ],
            (Object) [
                'validator' => 'required',
                'data' => !empty($request->files()->banner) ? $request->files()->banner : '',
                'key' => 'Product Banner',
            ],
            (Object) [
                'validator' => 'img',
                'data' => !empty($request->files()->banner) ? $request->files()->banner : '',
                'key' => 'Product Banner',
                'file_name' => 'banner',
                'acceptedExtension' => ['jpg', 'png', 'gif', 'jpeg'],
                'maxSize' => 5000000
            ],
        );

        $validationBag = parent::validation($validationObject);
        if ($validationBag->status) {
            $response->code(400)->json($validationBag);
            return;
        }

        // Work the banner image...
        $bannerPath = './public/img/';
        $bannerName = time() . '_' . basename($request->files()->banner['name']);
        if (!move_uploaded_file($request->files()->banner['tmp_name'], $bannerPath . $bannerName)) {

            $Response['status'] = 400;
            $Response['data'] = [];
            $Response['message'] = 'An unexpected error occuured and your file could not be uploaded. Please, try again later.';

            $response->code(400)->json($Response);
            return;
        }
        // create the product...
        $Payload = array(
            'name' => htmlentities(stripcslashes(strip_tags($Data->name))),
            'catalog_id' => (int) htmlentities(stripcslashes(strip_tags($Data->catalog_id))),
            'color' => htmlentities(stripcslashes(strip_tags($Data->color))),
            'price' => (float) htmlentities(stripcslashes(strip_tags($Data->price))),
            'size' => \htmlentities(\stripcslashes(strip_tags($Data->size))),
            'banner' => 'public/img/' . $bannerName,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        try {
            $Task = new Task();
            $product = $Task::createProduct($Payload);
            if ($product['status']) {
                $Response['status'] = 201;
                $Response['data'] = $product['data'];
                $Response['message'] = '';

                $response->code(201)->json($Response);
                return;
            }

            $Response['status'] = 400;
            $Response['data'] = [];
            $Response['message'] = 'An unexpected error occurred and your product could not be created. Please, try again later.';

            $response->code(400)->json($Response);
            return;
        } catch (Exception $e) {
            $Response['status'] = 500;
            $Response['message'] = $e->getMessage();
            $Response['data'] = [];

            $response->code(500)->json($Response);
            return;
        }
    }

    /**
     * updateProduct
     *
     * Updates a Product.
     *
     * @param mixed $request $response Contains the Request and Respons Object from the router.
     * @return mixed Anonymous
     */
    public function updateProduct($request, $response)
    {
        $Response = [];
        // Call the JSON Middleware
        $Task = new Task();
        $FormDataMiddleware = new RequestMiddleware();
        $formData = $FormDataMiddleware::acceptsFormData();

        if (!$formData) {
            array_push($Response, [
                'status' => 400,
                'message' => 'Sorry, Only Multipart Form Data Contents are allowed to access this Endpoint.',
                'data' => []
            ]);

            $response->code(400)->json($Response);
            return;
        }

        $JwtMiddleware = new JwtMiddleware();
        $jwtMiddleware = $JwtMiddleware::getAndDecodeToken();
        if (isset($jwtMiddleware) && $jwtMiddleware == false) {
            $response->code(400)->json([
                'status' => 401,
                'message' => 'Sorry, the authenticity of this token could not be verified.',
                'data' => []
            ]);
            return;
        }

        $Data = $request->paramsPost();
        $validationObject = array(
            (Object) [
                'validator' => 'required',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product ID'
            ],
            (Object) [
                'validator' => 'taskExists',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product Id'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->name) ? $Data->name : '',
                'key' => 'Product Name'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->catalog_id) ? $Data->catalog_id : '',
                'key' => 'Product Catalog'
            ],
            (Object) [
                'validator' => 'toDoListExists',
                'data' => isset($Data->catalog_id) ? $Data->catalog_id : '',
                'key' => 'Product Catalog'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->price) ? $Data->price : '',
                'key' => 'Product Price'
            ],
            (Object) [
                'validator' => 'numeric',
                'data' => isset($Data->price) ? $Data->price : '',
                'key' => 'Product Price'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->color) ? $Data->color : '',
                'key' => 'Product Color'
            ],
            (Object) [
                'validator' => 'string',
                'data' => isset($Data->color) ? $Data->color : '',
                'key' => 'Product Color'
            ],
            (Object) [
                'validator' => 'required',
                'data' => isset($Data->size) ? $Data->size : '',
                'key' => 'Product Size'
            ],
            (Object) [
                'validator' => !empty($request->files()->banner) ? 'img' : 'nullable',
                'data' => !empty($request->files()->banner) ? $request->files()->banner : '',
                'key' => 'Product Banner',
                'file_name' => 'banner',
                'acceptedExtension' => ['jpg', 'png', 'gif', 'jpeg'],
                'maxSize' => 5000000
            ],
        );

        $validationBag = parent::validation($validationObject);
        if ($validationBag->status) {
            $response->code(400)->json($validationBag);
            return;
        }

        // Work the banner image...
        $banner = 'public/img/';
        if (!empty($request->files()->banner)) {

            $product = $Task::findProductById($request->id)['data'];
            if (file_exists($product['banner'])) {
                unlink($product['banner']);
            }

            $bannerPath = './public/img/';
            $bannerName = time() . '_' . basename($request->files()->banner['name']);
            if (!move_uploaded_file($request->files()->banner['tmp_name'], $bannerPath . $bannerName)) {

                $Response['status'] = 400;
                $Response['data'] = [];
                $Response['message'] = 'An unexpected error occuured and your file could not be uploaded. Please, try again later.';

                $response->code(400)->json($Response);
                return;
            }

            $banner .= $bannerName;
        }


        // create the product...
        $Payload = array(
            'id' => $request->id,
            'name' => htmlentities(stripcslashes(strip_tags($Data->name))),
            'catalog_id' => (int) htmlentities(stripcslashes(strip_tags($Data->catalog_id))),
            'color' => htmlentities(stripcslashes(strip_tags($Data->color))),
            'price' => (float) htmlentities(stripcslashes(strip_tags($Data->price))),
            'size' => \htmlentities(\stripcslashes(strip_tags($Data->size))),
            'banner' => $banner,
            'updated_at' => date('Y-m-d H:i:s')
        );

        try {
            $product = $Task::updateProduct($Payload);
            if ($product['status']) {
                $product['data'] = $Task::findProductById($request->id)['data'];
                $Response['status'] = 200;
                $Response['data'] = $product['data'];
                $Response['message'] = '';

                $response->code(200)->json($Response);
                return;
            }

            $Response['status'] = 400;
            $Response['data'] = [];
            $Response['message'] = 'An unexpected error occurred and your product could not be updated. Please, try again later.';

            $response->code(400)->json($Response);
            return;
        } catch (Exception $e) {
            $Response['status'] = 500;
            $Response['message'] = $e->getMessage();
            $Response['data'] = [];

            $response->code(500)->json($Response);
            return;
        }
    }

    /**
     * getProductById
     *
     * Gets a Product by it's ID
     *
     * @param mixed $request $response Contains the Request and Respons Object from the router.
     * @return mixed Anonymous
     */
    public function getProductById($request, $response)
    {
        $Response = [];
        // Call the Middleware
        $Task = new Task();

        $JwtMiddleware = new JwtMiddleware();
        $jwtMiddleware = $JwtMiddleware::getAndDecodeToken();
        if (isset($jwtMiddleware) && $jwtMiddleware == false) {
            $response->code(400)->json([
                'status' => 401,
                'message' => 'Sorry, the authenticity of this token could not be verified.',
                'data' => []
            ]);
            return;
        }

        $validationObject = array(
            (Object) [
                'validator' => 'required',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product ID'
            ],
            (Object) [
                'validator' => 'taskExists',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product Id'
            ],
        );

        $validationBag = parent::validation($validationObject);
        if ($validationBag->status) {
            $response->code(400)->json($validationBag);
            return;
        }

        try {
            $Task = new Task();
            $product = $Task::findProductById($request->id);

            if ($product['status']) {
                $Response['status'] = 200;
                $Response['data'] = $product['data'];
                $Response['message'] = '';

                $response->code(200)->json($Response);
                return;
            }

            $Response['status'] = 400;
            $Response['data'] = [];
            $Response['message'] = 'An unexpected error occurred and your product could not be retrieved. Please, try again later.';

            $response->code(400)->json($Response);
            return;
        } catch (Exception $e) {
            $Response['status'] = 500;
            $Response['message'] = $e->getMessage();
            $Response['data'] = [];

            $response->code(500)->json($Response);
            return;
        }
    }

    /**
     * deleteProduct
     *
     * Deletes a Product by it'd ID
     *
     * @param mixed $request $response Contains the Request and Respons Object from the router.
     * @return mixed Anonymous
     */
    public function deleteProduct($request, $response)
    {
        $Response = [];
        // Call the Middleware
        $Task = new Task();

        $JwtMiddleware = new JwtMiddleware();
        $jwtMiddleware = $JwtMiddleware::getAndDecodeToken();
        if (isset($jwtMiddleware) && $jwtMiddleware == false) {
            $response->code(400)->json([
                'status' => 401,
                'message' => 'Sorry, the authenticity of this token could not be verified.',
                'data' => []
            ]);
            return;
        }

        $validationObject = array(
            (object)[
                'validator' => 'required',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product ID'
            ],
            (object)[
                'validator' => 'taskExists',
                'data' => isset($request->id) ? $request->id : '',
                'key' => 'Product Id'
            ],
        );

        $validationBag = parent::validation($validationObject);
        if ($validationBag->status) {
            $response->code(400)->json($validationBag);
            return;
        }

        try {
            $Task = new Task();
            $product = $Task::deleteProduct($request->id);

            if ($product['status']) {
                $Response['status'] = 200;
                $Response['data'] = [];
                $Response['message'] = '';

                $response->code(200)->json($Response);
                return;
            }

            $Response['status'] = 400;
            $Response['data'] = [];
            $Response['message'] = 'An unexpected error occurred and your product could not be deleted. Please, try again later.';

            $response->code(400)->json($Response);
            return;
        } catch (Exception $e) {
            $Response['status'] = 500;
            $Response['message'] = $e->getMessage();
            $Response['data'] = [];

            $response->code(500)->json($Response);
            return;
        }
    }
}