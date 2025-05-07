<?php

use \App\Models\Game;
use \App\Models\Player;

$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);
if ($game->status == GAME::STATUS_ROUND_RESULT) {
    $player->last_selected_answer = null;
    app(Player::class)->update($player, ['last_selected_answer']);
}