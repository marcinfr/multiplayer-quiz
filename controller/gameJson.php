<?php

require_once('model/player.php');
require_once('model/game.php');

$data = [];

$player = app(Player::class)->getCurrentPlayer();
$game = app(Game::class)->getByPlayer($player);

if ($game->round < 1) {
    if ($player->is_host) {
        $data['start'] = [];
    } else {
        $data['wait'] = [];
    }
}

if ($game->round < 1) {
    $playersHtml = getPlayersListHtml($game);
    if ($playersHtml) {
        $data['players']['list'] = $playersHtml;
    }
} else {
    $question = app(Game::class)->getQuestion($game);
    $data['question']['question-number'] = $game->round;
    $data['question']['question-text'] = $question['question'];
    foreach ($question['answers'] as $i => $answer) {
        $data['answers']['answer-' . $i] = $answer['answer'];
    }
}

function getPlayersListHtml($game)
{
    $html = '';
    foreach(app(Game::class)->getPlayers($game) as $player) {
        $html .= '<tr><td>' . $player->name . '<td></tr>';
    }
    return $html;
}

echo json_encode($data);