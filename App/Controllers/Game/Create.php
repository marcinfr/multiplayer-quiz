<?php

namespace App\Controllers\Game;

class Create extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $questions = $this->getRequest()->getParam('questions');
        if (!$questions) {
            throw new \Exception("Nie wybrałeś pytań");
        } 

        $config = [
            'questions' => $questions,
        ];

        $game = (object) [
            'creator' => $this->getRequest()->getParam('name'),
            'status' => \App\Models\Game::STATUS_RESULT,
            'config' => json_encode($config),
        ];

        app(\App\Models\Game::class)->save($game);

        return app(\App\Response\Redirect::class)
            ->setUrl('join?game_id=' . $game->id);
    }
}
