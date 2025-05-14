<?php

namespace App\Controllers\Game;

class Create extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $questions = $this->getRequest()->getParam('questions');
        foreach($questions as $providerCode => $config) {
            $isSelected = $config['selected'] ?? false;
            if (!$isSelected) {
                unset($questions[$providerCode]);
            }
        }
        
        $config = [
            'questions' => $questions,
        ];

        $player = app(\App\Models\Player::class)->getCurrentPlayer();

        $game = (object) [
            'creator' => $player->name,
            'status' => \App\Models\Game::STATUS_RESULT,
            'config' => json_encode($config),
        ];

        app(\App\Models\Game::class)->save($game);

        return app(\App\Response\Redirect::class)
            ->setUrl('join?game_id=' . $game->id);
    }
}
