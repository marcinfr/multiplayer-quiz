<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Response;

class Redirect
{
    private $url;

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }


    public function sendResponse()
    {
        header("Location: " . $this->url);
    }
}