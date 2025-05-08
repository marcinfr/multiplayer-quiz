<?php

namespace App;

class Router
{
    public function handle()
    {
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = parse_url($requestUri, PHP_URL_PATH);
        $route = ltrim(substr($path, strlen($basePath)), '/');

        $routeParts = explode('/', $route);
        foreach ($routeParts as $key => $part) {
            $routeParts[$key] = ucfirst($part);
        }
        $route = implode('\\', $routeParts);

        if (!$route) {
            $route = 'Index';
        }

        $class = "\App\Controllers\\" . $route;

        if (class_exists($class)) {
            // do nothing
        } elseif (class_exists($class . 'Action')) {
            $class = $class . 'Action';
        } else {
            http_response_code(404);
            echo "404 - Nie znaleziono strony.";
            exit();
        }


        $request = new \App\Request();
        $controller = new $class();
        $controller->dispatch($request);
    }
}