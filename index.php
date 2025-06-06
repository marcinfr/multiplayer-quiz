<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/App/app.php';

$response = app(\App\Router::class)->handle();
if ($response) {
    $response->sendResponse();
}
