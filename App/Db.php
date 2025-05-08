<?php

namespace App;

class Db
{
    private $connection;

    public function getConnection()
    {
        if ($this->connection === null) {
            $config = require(__DIR__ . '/../config/db.php');
            $host = $config['DB_HOST'];
            $user = $config['DB_USER'];
            $password = $config['DB_PASSWORD'];
            $database = $config['DB_DATABASE'];
            $this->connection = new \mysqli($host, $user, $password, $database);
            if ($this->connection->connect_error) {
                throw new \Exception("DB connection error");
            }
        }
        return $this->connection;
    }
}