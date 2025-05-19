<?php

use App\Models\Game;

class GenerateQuestion
{
    public $command = 'generate-question';

    public function execute($gameId = false)
    {
        if (!$gameId) {
            return;
        }

        $game = app(Game::class)->getGame($gameId);
        if ($game) {
            app(Game::class)->nextRound($game);
        }
    }
}