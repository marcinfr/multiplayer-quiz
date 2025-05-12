<?php

namespace App\Controllers;

use App\Models\Player;

class SelectGame extends AbstractController
{
    public function execute()
    {
        $db = app(\App\Db::class);
        $connection = $db->getConnection();
        $player = app(Player::class)->getCurrentPlayer();
        $sql = "select id,creator from game where round = 0";
        $activeGames = $connection->query($sql);
        if (!$activeGames) {
            $activeGames = [];
        }

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

