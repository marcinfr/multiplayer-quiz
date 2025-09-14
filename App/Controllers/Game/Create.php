<?php

namespace App\Controllers\Game;

class Create extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $questions = $this->getRequest()->getParam('questions');
        $questionProviders = app(\App\Models\Game::class)->getQuestionProviders();
        foreach($questions as $providerCode => $config) {

            if (!isset($questionProviders[$providerCode])) {
                unset($questions[$providerCode]);
                continue;
            }

            $questionProvider = $questionProviders[$providerCode];
            if (!$questionProvider->validateConfig($config)) {
                unset($questions[$providerCode]);
                continue;
            }

            $isSelected = $config['selected'] ?? false;
            if (!$isSelected) {
                unset($questions[$providerCode]);
            }
        }

        if (empty($questions)) {
            return app(\App\Response\Redirect::class)
                ->setUrl('form');
        }

        $config = [
            'questions' => $questions,
            'game' => $this->getRequest()->getParam('config', []),
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
