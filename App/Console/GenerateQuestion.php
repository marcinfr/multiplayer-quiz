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
            if (app(Game::class)->isGameEnd($game)) {
                app(Game::class)->reset($game);
            }

            app(Game::class)->nextRound($game);
        }
    }
}