<?php

declare(strict_types=1);

namespace App\routes;

class RouteConfiguration
{
    public string $route;
    public string $controller;
    public string $action;
    public string $name;
    public string $middleware;

    /**
     * @param string $route
     * @param string $controller
     * @param string $action
     */
    public function __construct(string $route, string $controller, string $action)
    {
        $this->route = $route;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function name(string $name): RouteConfiguration
    {
        $this->name = $name;

        return $this;
    }

    public function middleware(string $middleware): RouteConfiguration
    {
        $this->middleware = $middleware;

        return $this;
    }
}