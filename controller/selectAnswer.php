<?php

require_once('model/player.php');

$answerId = $_GET['id'];
$player = app(Player::class)->getCurrentPlayer();
if (!$player->last_selected_answer && $answerId) {
    app(Player::class)->update($player, [
        'last_selected_answer' => $answerId
    ]);
}