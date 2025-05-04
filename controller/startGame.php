<?php

require_once('model/player.php');
require_once('model/game.php');

$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);

if ($game->round < 1 && $player->is_host) {
    app(Game::class)->update($game, ['round' => 1]);
}