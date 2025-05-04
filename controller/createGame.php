<?php

require_once('model/db.php');
require_once('model/session.php');

$db = app(DB::class);
$connection = $db->getConnection();

$session = app(Session::class);

$name = $_POST['name'];
$gameId = $_POST['game_id'];
$isHost = 0;
$sessionId = $session->getSessionId();

if (!$gameId) {
    $isHost = 1;
    $sql = "insert into game (creator) values('$name')";
    if ($connection->query($sql)) {
        $gameId = $connection->insert_id;
    } else {
        throw new \Exception("Błąd zapisu");
    }
}

$sql = "insert into player (game_id, name, session_id, is_host) values($gameId, '$name', '$sessionId', $isHost)";
if (!$connection->query($sql)) {
    throw new \Exception("Błąd zapisu");
}

header("Location: game");

