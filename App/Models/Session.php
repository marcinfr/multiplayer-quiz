<?php

namespace App\Models;

class Session
{
    private $sessionId;

    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
        $this->sessionId = session_id();
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }
}