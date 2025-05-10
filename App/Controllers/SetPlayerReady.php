<?php

namespace App\Controllers;

use \App\Models\Game;
use \App\Models\Player;

class SetPlayerReady extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(Player::class)->getCurrentPlayer();
        $game = app(Game::class)->getByPlayer($player);
        if ($game->status == GAME::STATUS_RESULT) {
            $player->has_answer = null;
            app(Player::class)->update($player, ['has_answer']);
        }
    }
}
