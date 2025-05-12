<?php

namespace App\Controllers;

class Game extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        $game = app(\App\Models\Game::class)->getByPlayer($player);
        $content = new \App\Block\Template('game.phtml', ['game' => $game]);
        app(\App\Block\Page::class)->addJs('pub/js/game1.js');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}