<?php

namespace App\Models;

class Game extends DataObject
{
    const STATUS_QUESTION = 'question';
    const STATUS_ANSWER = 'answer';
    const STATUS_RESULT = 'result';

    protected string $table = 'game';
    private $games = [];

    public function getByPlayer($player)
    {
        if (!isset($this->games[$player->id])) {
            $db = app(\App\DB::class);
            $connection = $db->getConnection();
            $sql = 'select * from game where id = ' . $player->game_id;
            $game = $connection->query($sql)->fetch_object();
            $game->config = json_decode($game->config);
            $this->games[$player->id] = $game;
        }
        return $this->games[$player->id];
    }

    public function getPlayers($game)
    {
        if (!isset($game->players)) {
            $game->players = [];
            $db = app(\App\DB::class);
            $connection = $db->getConnection();

            $sql = 'select * from player where game_id = ' . $game->id;
            $sql .= ' order by total_points ASC';
            $result = $connection->query($sql);
            while ($player = $result->fetch_object()) {
                $game->players[] = $player;
            }
        }
        return $game->players;
    }

    public function getQuestion($game)
    {
        if (!isset($game->current_question)) {
            $this->randomQuestion($game);
        }
        if (!isset($game->question)) {
            $game->question = json_decode($game->current_question, true);
        }
        return $game->question;
    }

    public function randomQuestion($game)
    {
        $quizIds = $game->config->quiz_ids ?? [];
        $questions = [];
        foreach ($quizIds as $quizId) {
            $quizQesitions = Quiz::getQuestions($quizId);
            $questions = array_merge($questions, $quizQesitions);
        }

        $randomQuestionId = array_rand($questions);
        $randomQuestion = $questions[$randomQuestionId];
        $answers = [[
            'answer' => $randomQuestion['answer'],
            'correct' => true,
        ]];
        $wrongAnswers = $randomQuestion['wrong_answers'] ?? [];
        foreach ($wrongAnswers as $answer) {
            $answers[] = [
                'answer' => $answer
            ];
        }

        shuffle($answers);

        $game->current_question = json_encode([
            'question' => $randomQuestion['question'],
            'answers' => $answers,
        ]);
        $this->update($game, ['current_question']);
    }

    public function updateStatus($game, $newStatus)
    {
        if ($newStatus != $game->status) {
            $game->status = $newStatus;
            $game->last_update_timestamp = time();
            self::update($game, ['status', 'last_update_timestamp', 'round']);
        }
    }

    public function nextRound($game)
    {
        if ($game->status !== self::STATUS_QUESTION) {
            $game->status = self::STATUS_QUESTION;
            $game->last_update_timestamp = time();
            $game->current_question = null;
            $game->round ++;
            self::update($game, ['status', 'last_update_timestamp', 'round', 'current_question']);
        }
    }

    public function hasTimeElapsedFromLastUpdate($game, int $seconds)
    {
        return time() - $seconds > $game->last_update_timestamp;
    }
}