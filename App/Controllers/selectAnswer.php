<?php

use \App\Models\Player;
use \App\Models\Game;

$answerId = $_GET['id'];
$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);

if ($game->status == GAME::STATUS_QUESTION
    && !$player->last_selected_answer 
    && $answerId
) {
    $player->last_selected_answer = $answerId;
    app(Player::class)->update($player, ['last_selected_answer']);
}