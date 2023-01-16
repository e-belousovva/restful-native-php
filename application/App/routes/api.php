<?php

namespace App\routes;

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ToDoListController;
use App\Http\Controllers\UserController;

/******************** User Routes || Authentication Routes **********************/
Route::post('/api/v1/user/login', [UserController::class, 'login']);
Route::post('/api/v1/user/register', [UserController::class, 'register']);

/******************** ToDoList Routes **********************/
Route::get('/api/v1/todolists', [ToDoListController::class, 'fetchToDoLists'])->middleware('jwt');
Route::get('/api/v1/todolists-export/{id}', [ToDoListController::class, 'exportToDoList'])->middleware('jwt');
Route::get('/api/v1/todolists/{id}', [ToDoListController::class, 'fetchToDoListById'])->middleware('jwt');
Route::post('/api/v1/todolists', [ToDoListController::class, 'createNewToDoList'])->middleware('jwt');
Route::put('/api/v1/todolists/{id}', [ToDoListController::class, 'updateToDoList'])->middleware('jwt');
Route::delete('/api/v1/todolists/{id}', [ToDoListController::class, 'deleteToDoList'])->middleware('jwt');

/******************** Task Routes  **********************/
Route::get('/api/v1/todolists/{listId}/tasks', [TaskController::class, 'fetchTasks'])->middleware('jwt');
Route::get('/api/v1/todolists/{listId}/tasks/{taskId}', [TaskController::class, 'getTaskById'])->middleware('jwt');
Route::post('/api/v1/todolists/{listId}/tasks', [TaskController::class, 'createTask'])->middleware('jwt');
Route::put('/api/v1/todolists/{listId}/tasks/{taskId}', [TaskController::class, 'updateTask'])->middleware('jwt');
Route::delete('/api/v1/todolists/{listId}/tasks/{taskId}', [TaskController::class, 'deleteTask'])->middleware('jwt');

