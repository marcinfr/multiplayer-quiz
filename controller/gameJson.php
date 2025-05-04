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
    $answersListHtml = '';
    foreach ($question['answers'] as $id => $answer) {
        $id += 1;
        $class = 'answer';
        if ($id == $player->last_selected_answer) {
            $class .= ' selected';
        } else {
            $class .= ' not-selected';
        }
        $data['answers']['answers-list'] .= '<div class="' . $class. '" data-index="' . $id. '">' . $answer['answer'] . '</div>';
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

$data['hash'] = md5(json_encode($data));
echo json_encode($data);