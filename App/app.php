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