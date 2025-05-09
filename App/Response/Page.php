<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Response;

class Page
{
    public function sendResponse()
    {
        echo app(\App\Block\Page::class)->getHtml();
    }
}