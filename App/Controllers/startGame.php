<?php

use \App\Models\Player;
use \App\Models\Game;

$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);

if ($game->round < 1 && $player->is_host) {
    app(Game::class)->nextRound($game);
}