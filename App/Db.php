<?php

namespace App;

class Db
{
    private $connection;

    public function getConnection()
    {
        if ($this->connection === null) {
            $config = config();
            $dbConfig = $config['DB'];
            $host = $dbConfig['HOST'];
            $user = $dbConfig['USER'];
            $password = $dbConfig['PASSWORD'];
            $database = $dbConfig['DATABASE'];
            $this->connection = new \mysqli($host, $user, $password, $database);
            if ($this->connection->connect_error) {
                throw new \Exception("DB connection error");
            }
        }
        return $this->connection;
    }
}