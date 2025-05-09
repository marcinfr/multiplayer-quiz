<?php

namespace App\Controllers;

class SelectGame extends AbstractController
{
    public function execute()
    {
        $db = app(\App\Db::class);
        $connection = $db->getConnection();
        $sql = "select id,creator from game where round = 0";
        $activeGames = $connection->query($sql);
        if (!$activeGames) {
            $activeGames = [];
        }
        $template = new \App\Block\Template('game/select.phtml',
            [
                'active_games' => $activeGames
            ]
        );
        $template->render();
    }
}

