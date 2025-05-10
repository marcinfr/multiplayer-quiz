<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once("App/app.php");

function createTable(string $name, array $columns)
{
    $db = app(\App\Db::class);
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
        'current_question text',
        'status varchar(255)',
        'last_update_timestamp int unsigned',
        'config text'
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
        'has_answer smallint unsigned default 0',
        'total_points smallint unsigned default 0',
        'last_activity_timestamp int unsigned'
    ]
);
