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
        if (!$player->id) {
            return false;
        }
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
                if ($player->view_type !== \App\Models\Player::VIEW_TYPE_HOST) {
                    $game->players[] = $player;
                }
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

    public function getQuestionProviders()
    {
        return [
            'question_list' =>  app(\App\Models\Game\QuestionProvider\QuestionLists::class),
            'math_tasks' => app(\App\Models\Game\QuestionProvider\MathTasks::class),
            'countries' => app(\App\Models\Game\QuestionProvider\Countries::class),
        ];
    }

    public function randomQuestion($game)
    {
        $questions = (array) $game->config->questions;
        $sumOfPriorities = 0;
        foreach ($questions as $providerId => $config) {
            $sumOfPriorities += $config->priority ?? 0;
            $config->priority = $sumOfPriorities;
        }
        $random = rand(1, $sumOfPriorities);
        foreach ($questions as $providerId => $config) {
            if ($random <= $config->priority) {
                break;
            }
        }
        $questionProviders = $this->getQuestionProviders();
        $questionProvider = $questionProviders[$providerId];
        $game->current_question = $questionProvider->getQuestion((array) $questions[$providerId]);
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