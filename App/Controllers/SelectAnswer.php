<?php

namespace App\Controllers;

use \App\Models\Player;
use \App\Models\Game;

class SelectAnswer extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $answerId = (int) $this->getRequest()->getParam('id');
        $player = app(Player::class)->getCurrentPlayer();
        $game = app(Game::class)->getByPlayer($player);

        if ($game->status == GAME::STATUS_QUESTION
            && !$player->has_answer
        ) {
            $player->last_selected_answer = $answerId;
            $player->has_answer = true;
            if (app(Player::class)->hasCorrectAnswer($player, $game)) {
                $player->total_points ++;
            }
            app(Player::class)->update($player, [
                'last_selected_answer',
                'total_points',
                'has_answer'
            ]);
        }
    }
}