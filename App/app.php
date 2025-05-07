<?php

function app($class = null) {
    static $container = [];

    if ($class === null) {
        return $container;
    }

    if (!isset($container[$class])) {
        $container[$class] = new $class();
    }

    return $container[$class];
}