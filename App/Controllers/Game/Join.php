<?php

namespace App\Controllers\Game;

class Join extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        if ($name = $this->getRequest()->getParam('name')) {
            $player->name = $name;
        }
        if ($view_type = $this->getRequest()->getParam('view_type')) {
            $player->view_type = $view_type;
        }

        $player->game_id = $this->getRequest()->getParam('game_id');
        $player->is_host = 0;
        $player->has_answer = 1;
        $player->total_points = 0;
        $player->answered_questions_qty = 0;
        $player->correct_answered_questions_qty = 0;
        app(\App\Models\Player::class)->save($player);

        return app(\App\Response\Redirect::class)->setUrl('../game');
    }
} 