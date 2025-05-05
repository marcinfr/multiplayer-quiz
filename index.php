<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('app.php');

$request = $_SERVER['REQUEST_URI'];
$script_name = dirname($_SERVER['SCRIPT_NAME']);
$request = explode('/', $request);

unset($request[0]);
if ('/' . $request[1] == $script_name) {
    unset($request[1]);
}
$path = implode('/', $request);
$path = trim(parse_url($path, PHP_URL_PATH), '/');

if (!$path) {
    $path = 'index';
}

$file = 'controller/' . $path . '.php';

if (file_exists($file)) {
    require $file;
} else {
    http_response_code(404);
    echo "404 - Nie znaleziono strony.";
}