<?php

class DB
{
    private $connection;

    public function getConnection()
    {
        if ($this->connection === null) {
            $config = require('config.php');
            $host = $config['DB_HOST'];
            $user = $config['DB_USER'];
            $password = $config['DB_PASSWORD'];
            $database = $config['DB_DATABASE'];
            $this->connection = new mysqli($host, $user, $password, $database);
            if ($this->connection->connect_error) {
                throw new \Exception("DB connection error");
            }
        }
        return $this->connection;
    }
}