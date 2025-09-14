<?php

class CreateTables
{
    public $command = 'create-tables';

    public function execute()
    {
        $this->createTable(
            'game', 
            [
                'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY',
                'creator varchar(255)',
                'round smallint unsigned default 0',
                'current_question text',
                'status varchar(255)',
                'last_update_timestamp int unsigned',
                'config text',
                'questions_history text'
            ]
        );

        $this->createTable(
            'player', 
            [
                'id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
                'name varchar(255)',
                'session_id varchar(255)',
                'game_id int',
                'is_host smallint unsigned default 0',
                'last_selected_answer smallint unsigned default null',
                'has_answer smallint unsigned default 0',
                'answered_questions_qty int unsigned default 0',
                'correct_answered_questions_qty int unsigned default 0',
                'total_points smallint unsigned default 0',
                'player_rank smallint unsigned default null',
                'last_activity_timestamp int unsigned',
                'view_type varchar(255)',
            ]
        );
    }

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
}