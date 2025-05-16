<?php

namespace App\Controllers\Game;

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
            $player->answered_questions_qty ++;
            if (app(Player::class)->hasCorrectAnswer($player, $game)) {
                $player->total_points ++;
                $player->correct_answered_questions_qty ++;
            }
            app(Player::class)->save($player);
        }
    }
}