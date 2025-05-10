<?php

function app($class = null) {
    static $container = [];

    if ($class === null) {
        return $container;
    }

    if (!isset($container[$class])) {
        if (class_exists($class)) {
            $container[$class] = new $class();
        } else {
           throw new \App\Exceptions\ClassNotExists('Class ' . $class . ' not exists');
        }
    }

    return $container[$class];
}

function config()
{
    static $config;
    if ($config === null) {
        $config = require('config/app.php');
    }
    return $config;
}

function url($path)
{   
    $config = config();
    $rootDir = $config['ROOT_DIR'] ?? '';
    $rootDir = $rootDir ? '/' . $rootDir : '';
    return $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['HTTP_HOST'] . $rootDir  . '/' . $path;
}