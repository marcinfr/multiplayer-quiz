<?php

namespace App\Controllers;

use \App\Models\Player;
use \App\Models\Game;

class StartGame extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(Player::class)->getCurrentPlayer();
        $game = app(Game::class)->getByPlayer($player);
        
        if ($game->round < 1 && $player->is_host) {
            app(Game::class)->nextRound($game);
        }
    }
}
