<?php

require_once('model/db.php');
require_once('model/player.php');
require_once('model/game.php');

$db = app(DB::class);
$connection = $db->getConnection();

$name = $_POST['name'];
$gameId = $_POST['game_id'];
$isHost = 0;

$player = app(Player::class)->getCurrentPlayer();

if (!$gameId) {
    $isHost = 1;
    $game = (object) [
        'creator' => $name,
        'last_update_timestamp' => time(),
    ];
    app(Game::class)->save($game);
}

$player->game_id = $game->id;
$player->name = $name;
$player->is_host = $isHost;
$player->last_selected_answer = null;
$player->total_points = 0;
app(Player::class)->save($player);

header("Location: game");

