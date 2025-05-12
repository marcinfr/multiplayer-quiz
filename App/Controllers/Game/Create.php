<?php

namespace App\Controllers\Game;

class Create extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizIds = $this->getRequest()->getParam('quiz');
        if (!$quizIds) {
            throw new \Exception("Nie wybrałeś quizu");
        } 

        $config = [
            'quiz_ids' => $quizIds,
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
