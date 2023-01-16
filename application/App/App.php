<?php

declare(strict_types=1);

namespace App;

use App\routes\Route;
use App\routes\RouteDispatcher;

class App
{
    public static function run(): void
    {
        session_start();

        $requestMethod = ucfirst(strtolower($_SERVER['REQUEST_METHOD']));

        $methodName = 'getRoutes'.$requestMethod;

        foreach (Route::$methodName() as $routeConfiguration) {
            $routeDispatcher = new RouteDispatcher($routeConfiguration);
            $routeDispatcher->process();
        }

        http_response_code(404);
        echo 'Route not found!';
    }
}