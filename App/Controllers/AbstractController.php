<?php

namespace App\Controllers;

abstract class AbstractController
{
    abstract public function execute();

    public function getRequest()
    {
        return app(\App\Request::class);
    }

    public function dispatch()
    {
        return $this->execute();
    }
}