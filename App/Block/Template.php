<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Block;

class Template
{
    protected $template;
    private $data = [];
    private $childs = [];

    public function __construct(string $template = null, $data = [])
    {
        if ($template) {
            $this->template = $template;
        }
        $this->data = $data;
    }

    public function getData($key)
    {
        return $this->data[$key] ?? null;
    }

    public function getHtml()
    {
        if ($this->template) {
            ob_start();
            include __DIR__ . '/../../view/templates/' . $this->template;
            return ob_get_clean();
        }
    }

    public function addChild($child, string $name = null)
    {
        if ($name) {
            $this->childs[$name] = $child;
        } else {
            $this->childs[] = $child;
        }
        return $this;
    }

    public function getChild($name)
    {
        $child =  $this->childs[$name] ?? false;
        if ($child) {
            return $child->gethtml();
        }
        return '';
    }
}