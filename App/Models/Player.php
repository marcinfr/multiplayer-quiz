<?php

namespace App\Models;

class Player extends DataObject
{
    const VIEW_TYPE_HOST = "host";
    const VIEW_TYPE_NORMAL = "normal";
    const VIEW_TYPE_CONTROLLER = "controller";

    protected string $table = 'player';
    private $players = [];

    public function getBySessionId($sessionId)
    {
        if (!isset($this->players[$sessionId])) {
            $db = app(\App\Db::class);
            $connection = $db->getConnection();
            $sql = 'select * from player where session_id = "' . $sessionId . '"';
            $result = $connection->query($sql);
            if (!$result) {
                throw new \Exception($connection->error);
            }
            $player = $result->fetch_object();
            if ($player === null) {
                $player = (object) [
                    'session_id' =>  $sessionId
                ];
            }
            $this->players[$sessionId] = $player;
        }
        return $this->players[$sessionId];
    }

    public function getCurrentPlayer()
    {
        $session = app(\App\Session::class);
        return $this->getBySessionId($session->getSessionId());
    }

    public function hasCorrectAnswer($player, $game)
    {
        $answerId = $player->last_selected_answer;
        $question = app(\App\Models\Game::class)->getQuestion($game);
        $answer = $question['answers'][$answerId] ?? null;
        return $answer['correct'] ?? false;
    }

    public function getViewTypeOptions($player)
    {
        $options = [
            self::VIEW_TYPE_HOST => [
                'label' => 'Host',
                'value' => self::VIEW_TYPE_HOST,
                'selected' => false,
            ],
            self::VIEW_TYPE_NORMAL => [
                'label' => 'Normal',
                'value' => self::VIEW_TYPE_NORMAL,
                'selected' => false,
            ],
            self::VIEW_TYPE_CONTROLLER => [
                'label' => 'Kontroler',
                'value' => self::VIEW_TYPE_CONTROLLER,
                'selected' => false,
            ],
        ];

        $viewType = $player->view_type ?? self::VIEW_TYPE_NORMAL;
        $selected = $options[$viewType] ?  $viewType : self::VIEW_TYPE_NORMAL;
        $options[$selected]['selected'] = true;

        return $options;
    }
} 