<?php

namespace App\Models;

class Player extends DataObject
{
    protected string $table = 'player';
    private $players = [];

    public function getBySessionId($sessionId)
    {
        if (!isset($this->players[$sessionId])) {
            $db = app(\App\Db::class);
            $connection = $db->getConnection();
            $sql = 'select * from player where session_id = "' . $sessionId . '"';
            $player = $connection->query($sql)->fetch_object();
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
} 