<?php

namespace App\Controllers\Game;

class Form extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $content = new \App\Block\Template('game/form.phtml');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}