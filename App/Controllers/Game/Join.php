<?php

namespace App\Controllers\Game;

class Join extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $name = $this->getRequest()->getParam('name');
        $gameId = $this->getRequest()->getParam('game_id');
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        
        $player->game_id = $gameId;
        $player->name = $name;
        $player->is_host = 0;
        $player->has_answer = 1;
        $player->total_points = 0;
        app(\App\Models\Player::class)->save($player);

        return app(\App\Response\Redirect::class)->setUrl('../game');
    }
} 