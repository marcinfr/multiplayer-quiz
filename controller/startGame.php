<?php

require_once('model/player.php');
require_once('model/game.php');

$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);

if ($game->round < 1 && $player->is_host) {
    $game->round = 1;
    $game->status = Game::STATUS_QUESTION;
    app(Game::class)->update($game, ['round', 'status']);
}