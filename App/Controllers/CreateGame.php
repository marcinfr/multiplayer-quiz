<?php

namespace App\Controllers;

class CreateGame extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $name = $_POST['name'];
        $gameId = $_POST['game_id'];
        $isHost = 0;

        $player = app(\App\Models\Player::class)->getCurrentPlayer();

        if (!$gameId) {
            $isHost = 1;
            $game = (object) [
                'creator' => $name
            ];
            app(\App\Models\Game::class)->save($game);
            $gameId = $game->id;
        }

        $player->game_id = $gameId;
        $player->name = $name;
        $player->is_host = $isHost;
        $player->last_selected_answer = null;
        $player->total_points = 0;
        app(\App\Models\Player::class)->save($player);

        header("Location: game");
    }
}

