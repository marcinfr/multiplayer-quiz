<?php

namespace App\Controllers;

abstract class AbstractController
{
    private $request;

    abstract public function execute();

    public function getRequest()
    {
        return $this->request;
    }

    public function dispatch($request)
    {
        $this->request = $request;
        $this->execute();
    }
}