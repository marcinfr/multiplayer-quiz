<?php

namespace App\Controllers;

use App\Models\Player;

class SelectGame extends AbstractController
{
    public function execute()
    {
        $activeGames = app(\App\Models\Game::class)->getActiveGames();
        $player = app(Player::class)->getCurrentPlayer();

        $content = new \App\Block\Template('game/select.phtml',
            [
                'player' => $player,
                'active_games' => $activeGames,
                'view_types' => app(Player::class)->getViewTypeOptions($player),
            ]
        );
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}

