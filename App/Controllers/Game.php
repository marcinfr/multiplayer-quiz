<?php

namespace App\Controllers;

class Game extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        $game = app(\App\Models\Game::class)->getByPlayer($player);
        if (!$game) {
            return app(\App\Response\Redirect::class)->setUrl(url(''));
        }

        $content = new \App\Block\Template('game.phtml', [
            'game' => $game,
            'player' => $player,
        ]);
        app(\App\Block\Page::class)->addJs('pub/js/game3.js');
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}