<?php

namespace App\Models;

class Game extends DataObject
{
    const STATUS_QUESTION = 'question';
    const STATUS_ANSWER = 'answer';
    const STATUS_RESULT = 'result';

    protected string $table = 'game';
    private $games = [];

    private $activeGames;

    public function getActiveGames()
    {
        if ($this->activeGames == null) {
            $db = app(\App\Db::class);
            $connection = $db->getConnection();
            $time = time() - 30;

            $stmt = $connection->prepare("SELECT game_id FROM player WHERE last_activity_timestamp > ? and game_id is not NULL");
            $stmt->bind_param("s", $time); // "s" oznacza string; użyj "i" jeśli $time to liczba (np. timestamp)
            $stmt->execute();
            $result = $stmt->get_result();
            $activeGameIds = [];
            while ($row = $result->fetch_assoc()) {
                $activeGameIds[] = $row['game_id'];
            }
            if ($activeGameIds) {
                $sql = 'select * from game where id in (' . implode(',', $activeGameIds) . ')';
                $this->activeGames = $connection->query($sql);
            }
            if (!$this->activeGames) {
                $this->activeGames = [];
            }
            $this->removeInactiveGames($activeGameIds);
        }
        return $this->activeGames;
    }

    /**
     * @TODO napisac to ladniej
     */
    protected function removeInactiveGames($activeGameIds)
    {
        $db = app(\App\Db::class);
        $connection = $db->getConnection();
        if ($activeGameIds) {
            $where = ' where game_id not in (' . implode(',', $activeGameIds) .')';
        } else {
            $where = '';
        }
        $sql = 'delete from player' . $where;
        $connection->query($sql);
        if ($activeGameIds) {
            $where = ' where id not in (' . implode(',', $activeGameIds) .')';
        } else {
            $where = '';
        }
        $sql = 'delete from game' . $where;
        $connection->query($sql);
    }

    public function getByPlayer($player)
    {
        if (!$player->id) {
            return false;
        }
        if (!isset($this->games[$player->id])) {
            $db = app(\App\Db::class);
            $connection = $db->getConnection();
            $sql = 'select * from game where id = ' . $player->game_id;
            $game = $connection->query($sql)->fetch_object();
            $game->config = json_decode($game->config);
            $this->games[$player->id] = $game;
        }
        return $this->games[$player->id];
    }

    public function getHostPlayer($game)
    {
        $players = $this->getPlayers($game);
        $hostPlayer = $game->host_player ?? false;
        if (!$hostPlayer) {
            $hostPlayer = reset($players);
            if ($hostPlayer) {
                $hostPlayer->is_host = true;
                app(Player::class)->update($hostPlayer, ['is_host']);
                $game->host_player = $hostPlayer;
            }
        }
    }

    /**
     * get players with host types
     */
    public function getAllPlayers($game)
    {
        $this->getPlayers($game);
        return $game->all_players;
    }

    /**
     * get players without hosts
     */
    public function getPlayers($game)
    {
        if (!isset($game->players)) {
            $game->players = [];
            $game->all_players = [];
            $db = app(\App\DB::class);
            $connection = $db->getConnection();

            $sql = 'select * from player where game_id = ' . $game->id;
            $sql .= ' order by total_points DESC';
            $result = $connection->query($sql);
            while ($player = $result->fetch_object()) {
                if ($player->is_host) {
                    $game->host_player = $player;
                }
                $game->all_players[] = $player;
                if ($player->view_type !== \App\Models\Player::VIEW_TYPE_HOST) {
                    $game->players[] = $player;
                }
            }
        }
        return $game->players;
    }

    public function getQuestion($game, $canCreateQuestion = false)
    {
        if (!isset($game->current_question) && $canCreateQuestion) {
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