<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Response;

class Json
{
    private $json;

    public function setJson($json)
    {
        $this->json = $json;
        return $this;
    }


    public function sendResponse()
    {
        echo json_encode($this->json);
    }
}