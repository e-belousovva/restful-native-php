<?php
declare(strict_types=1);

namespace App\routes;

class RouteDispatcher
{
    private RouteConfiguration $routeConfiguration;

    private string $requestUri = '/';

    private array $paramMap = [];

    private array $paramRequestMap = [];

    public function __construct(RouteConfiguration $routeConfiguration)
    {
        $this->routeConfiguration = $routeConfiguration;
    }

    public function process()
    {
        // 1. If there is a query string, you need to clean (delete / at the beginning and at the end) it up and save it
        $this->saveRequestUri();
        // 2. Split the route string into an array and save the parameter and its name
        $this->setParamMap();
        // 3. Split the request string into an array and check is there a parameter like in an array (if so, put it into a regular expression)
        $this->makeRegexRequest();
        // 4. Run Controller and action
        $this->run();
    }

    private function saveRequestUri(): void
    {
        if ($_SERVER['REQUEST_URI'] !== $this->requestUri) {
            $this->requestUri = $this->clean($_SERVER['REQUEST_URI']);
            $this->routeConfiguration->route = $this->clean($this->routeConfiguration->route);
        }
    }

    private function clean(string $string): array|string|null
    {
        return preg_replace('/(^\/)|(\/$)/', '', $string);
    }

    private function setParamMap(): void
    {
        $routeArray = explode('/', $this->routeConfiguration->route);

        foreach ($routeArray as $paramKey => $param) {
            if (preg_match('/{.*}/', $param)) {
                $this->paramMap[$paramKey] = preg_replace('/(^\{)|(\}$)/', '', $param);
            }
        }
    }
    private function makeRegexRequest(): void
    {
        $requestUriArray = explode('/', $this->requestUri);

        foreach ($this->paramMap as $paramKey => $param) {
            if (!isset($requestUriArray[$paramKey])) {
                return;
            }

            $this->paramRequestMap[$param] = (int)$requestUriArray[$paramKey];
            $requestUriArray[$paramKey] = '{.*}';
        }

        $this->requestUri = implode('/', $requestUriArray);
        $this->prepareRegex();
    }

    private function prepareRegex(): void
    {
        $this->requestUri = str_replace('/', '\/', $this->requestUri);
    }

    /**
     * @throws \Exception
     */
    private function run()
    {
        if (preg_match("/$this->requestUri/", $this->routeConfiguration->route) && $this->checkMiddleware()) {
            $this->render();
        }
    }

    private function checkMiddleware()
    {
        if (isset($this->routeConfiguration->middleware)){
            $middlewareClassName = 'App\Http\Middleware\\' . ucfirst(strtolower($this->routeConfiguration->middleware)) . 'Middleware';
            $classMiddleware = (new $middlewareClassName);

            if ($classMiddleware) {
                return $classMiddleware->handle() ?? throw new \Exception('The authenticity of this token could not be verified.');
            }

            return true;
        }

        return true;
    }

    private function render()
    {
        $className = $this->routeConfiguration->controller;
        $action = $this->routeConfiguration->action;

        (new $className)->$action(...$this->paramRequestMap);
        session_destroy();

        die();
    }
}