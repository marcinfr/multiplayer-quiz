<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Block;

class Page extends Template
{
    protected $template = 'page.phtml';
    protected $js = [];

    public function addJs(string $js)
    {
        $this->js[] = $js;
    }
}