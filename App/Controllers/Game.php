<?php

namespace App\Controllers;

class Game extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $content = new \App\Block\Template('game.phtml');
        app(\App\Block\Page::class)->addJs('pub/js/game1.js');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}