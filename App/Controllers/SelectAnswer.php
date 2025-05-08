<?php

namespace App\Controllers;

use \App\Models\Player;
use \App\Models\Game;

class SelectAnswer extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $answerId = $this->getRequest()->getParam('id');
        $player = app(Player::class)->getCurrentPlayer();
        $game = app(Game::class)->getByPlayer($player);

        if ($game->status == GAME::STATUS_QUESTION
            && !$player->last_selected_answer 
            && $answerId
        ) {
            $player->last_selected_answer = $answerId;
            app(Player::class)->update($player, ['last_selected_answer']);
        }
    }
}