<?php

namespace App\Models;

class Game extends DataObject
{
    const STATUS_QUESTION = 'question';
    const STATUS_ANSWER = 'answer';
    const STATUS_RESULT = 'result';

    const STATUS_WAITING_FOR_QUESTION = 'waiting_for_question';

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
            $stmt->bind_param("i", $time);
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
            $where = ' where game_id not in (' . implode(',', $activeGameIds) .') and ';
        } else {
            $where = ' where ';
        }
        $time = time() - 60 * 60 * 2; // keep player session for 2h
        $where .= ' last_activity_timestamp > ' . $time;
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
        if (!$player->game_id) {
            return false;
        }
        return $this->getGame($player->game_id);
    }

    public function getGame($gameId)
    {
        if (!isset($this->games[$gameId])) {
            $db = app(\App\Db::class);
            $connection = $db->getConnection();
            $sql = 'select * from game where id = ' . $gameId;
            $game = $connection->query($sql)->fetch_object();
            if ($game) {
                $game->config = json_decode((string) $game->config);
                $game->questions_history = json_decode((string) $game->questions_history, true) ?? [];
                $this->games[$gameId] = $game;
            }
        }
        return $this->games[$gameId] ?? false;
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

    public function getQuestion($game)
    {
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
            'the_most' => app(\App\Models\Game\QuestionProvider\TheMost::class),
            'ai' => app(\App\Models\Game\QuestionProvider\OpenAi::class),
        ];
    }

    public function getRandomQuestion($game)
    {
        $questions = (array) $game->config->questions;
        $sumOfPriorities = 0;
        foreach ($questions as $providerCode => $config) {
            $sumOfPriorities += $config->priority ?? 0;
            $config->priority = $sumOfPriorities;
        }
        $random = rand(1, $sumOfPriorities);
        foreach ($questions as $providerCode => $config) {
            if ($random <= $config->priority) {
                break;
            }
        }
        $questionProviders = $this->getQuestionProviders();
        $questionProvider = $questionProviders[$providerCode];
        $questionProvider->setProviderCode($providerCode);
        $questionProvider->setGame($game);
        return $questionProvider->getQuestion((array) $questions[$providerCode]);
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
        $game->current_question = $this->getRandomQuestion($game);
        $game->status = self::STATUS_QUESTION;
        $game->last_update_timestamp = time();
        $game->round ++;
        self::update($game);
    }

    public function hasTimeElapsedFromLastUpdate($game, int $seconds)
    {
        return time() - $seconds > $game->last_update_timestamp;
    }
}