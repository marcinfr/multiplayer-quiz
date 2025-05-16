<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Controllers\Game;

use App\Models\Player;

class ExitAction extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(Player::class)->getCurrentPlayer();
        if ($player->game_id) {
            $player->game_id = null;
            app(Player::class)->update($player, ['game_id']);
        }
        return app(\App\Response\Redirect::class)->setUrl(url(''));
    }
}
