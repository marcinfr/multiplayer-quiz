<?php

require_once('model/db.php');
require_once('model/quiz.php');


class Game
{
    private $games = [];

    public function getByPlayer($player)
    {
        if (!isset($this->games[$player->id])) {
            $db = app(DB::class);
            $connection = $db->getConnection();
            $sql = 'select * from game where id = ' . $player->game_id;
            $this->games[$player->id] = $connection->query($sql)->fetch_object();
        }
        return $this->games[$player->id];
    }

    public function getPlayers($game)
    {
        if (!isset($game->players)) {
            $game->players = [];
            $db = app(DB::class);
            $connection = $db->getConnection();

            $sql = 'select * from player where game_id = ' . $game->id;
            $result = $connection->query($sql);
            while ($player = $result->fetch_object()) {
                $game->players[] = $player;
            }
        }
        return $game->players;
    }

    public function update($game, $data)
    {
        $db = app(DB::class);
        $connection = $db->getConnection();
        $update = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = $connection->real_escape_string($value);
                $value = '"' . $value . '"';
            }
            $update[] = $key . '=' . $value;
        }
        $update = implode(',', $update);

        $sql = 'update game set ' . $update . ' where id = ' . $game->id;
        $connection->query($sql);
    }

    public function getQuestion($game)
    {
        if (!isset($game->current_question)) {
            $this->randomQuestion($game);
        }
        if (!isset($game->question)) {
            $game->question = json_decode($game->current_question, true);
        }
        return $game->question['question'];
    }

    public function randomQuestion($game)
    {
        $quizId = 3;
        $questions = Quiz::getQuestions($quizId);
        $randomQuestionId = array_rand($questions);
        $randomQuestion = Quiz::getQuestion($quizId, $randomQuestionId);
        $randomQuestion = json_encode($randomQuestion);
        $game->current_question = $randomQuestion;
        $this->update($game, [
            'current_question' => $game->current_question,
        ]);
    }
}