<?php

require_once('model/db.php');
require_once('model/session.php');

class Player
{
    private $players = [];

    public function getBySessionId($sessionId)
    {
        if (!isset($this->players[$sessionId])) {
            $db = app(DB::class);
            $connection = $db->getConnection();
            $sql = 'select * from player where session_id = "' . $sessionId . '"';
            $this->players[$sessionId] = $connection->query($sql)->fetch_object();
        }
        return $this->players[$sessionId];
    }

    public function getCurrentPlayer()
    {
        $session = app(Session::class);
        return $this->getBySessionId($session->getSessionId());
    }
} 