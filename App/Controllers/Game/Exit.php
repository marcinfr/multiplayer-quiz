<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Controllers\Game;

use App\Models\Game;

class ExitAction extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(\App\Models\Player::class)->getCurrentPlayer();
        app(Game::class)->deletePlayer($player);
        return app(\App\Response\Redirect::class)->setUrl(url(''));
    }
}
