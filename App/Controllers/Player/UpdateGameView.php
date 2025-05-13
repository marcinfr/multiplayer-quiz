<?php

namespace App\Controllers\Player;

class UpdateGameView extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        $player->view_type = $this->getRequest()->getParam('view_type');
        app(\App\Models\Player::class)->update($player, ['view_type']);
    }
}
