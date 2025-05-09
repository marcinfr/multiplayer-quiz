<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Block;

class Template
{
    private $template;
    private $data = [];

    public function __construct(string $template = null, $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function getData($key)
    {
        return $this->data[$key] ?? null;
    }

    public function render()
    {
        if ($this->template) {
            ob_start();
            include __DIR__ . '/../../view/templates/' . $this->template;
            echo ob_get_clean();
        }
    }
}