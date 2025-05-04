<?php

require_once("app.php");
require_once("model/db.php");

function createTable(string $name, array $columns)
{
    $db = app(DB::class);
    $connection = $db->getConnection();

    if ($connection->query("SHOW TABLES LIKE '$name'")->fetch_all()) {
        $connection->query("DROP TABLE $name");
    }
    $columns = implode(',', $columns);
    $connection->query("CREATE TABLE $name ($columns)");
}


createTable(
    'game', 
    [
         'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
         'creator varchar(255)',
         'round smallint unsigned default 0',
         'current_question text'
    ]
);

createTable(
    'player', 
    [
        'id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'name varchar(255)',
        'session_id varchar(255)',
        'game_id int',
        'is_host smallint unsigned default 0',
        'last_selected_answer smallint unsigned default null',
        'total_points smallint unsigned default 0'
    ]
);
