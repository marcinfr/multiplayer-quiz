<?php

namespace App\Controllers;

use \App\Models\Game;
use \App\Models\Player;

class GameJson extends \App\Controllers\AbstractController
{
    private $data = [];
    private $game;
    private $currentPlayer;

    private function getGame()
    {
        if ($this->game == null) {
            $player = $this->getCurrentPlayer();
            $this->game = app(Game::class)->getByPlayer($player);
        }
        return $this->game;
    }

    private function getCurrentPlayer()
    {
        if ($this->currentPlayer === null) {
            $this->currentPlayer = app(Player::class)->getCurrentPlayer();
        }
        return $this->currentPlayer;
    }

    private function getPlayers()
    {
        return app(Game::class)->getPlayers($this->getGame());
    }

    private function setQuestion()
    {
        $this->data['section-question'] = '';
        if ($this->getGame()->round < 1) {
            return;
        }

        if (!in_array($this->getGame()->status, 
            [
                Game::STATUS_QUESTION, 
                Game::STATUS_ANSWER
            ]
        )) {
            return;
        }

        $question = app(Game::class)->getQuestion($this->getGame());

        foreach ($question['answers'] as $id => $answer) {
            $selectedId = $this->getCurrentPlayer()->last_selected_answer;
            if ($selectedId !== null && $selectedId == $id + 1) {
                if ($this->getGame()->status == Game::STATUS_ANSWER) {
                    $isCorrect = $answer['correct'] ?? false;
                    if ($isCorrect) {
                        $class= 'correct';
                    } else {
                        $class = 'incorrect';
                    }
                } else {
                    $class .= ' selected';
                }
            } else {
                $class = 'not-selected';
            }
            $question['answers'][$id]['class'] = $class;
        }

        $questionBlock =  new \App\Block\Template('game/question.phtml', [
            'game' => $this->getGame(),
            'question' => $question,
        ]);

        $this->data['section-question'] = $questionBlock->getHtml();

        return;
    }

    private function setResult()
    {
        $this->data['section-result'] = '';
        if ($this->getGame()->status == Game::STATUS_RESULT) {
            $resultBlock = new \App\Block\Template('game/result.phtml', ['players' => $this->getPlayers()]);
            $this->data['section-result'] = $resultBlock->getHtml();
        }
    }

    private function isRoundEnded()
    {
        foreach ($this->getPLayers() as $player) {
            if (!$player->last_selected_answer) {
                return false;
            }
        }
        return true;
    }

    private function allPlayersReady()
    {
        foreach($this->getPlayers() as $player) {
            if ($player->last_selected_answer !== null) {
                return false;
            }
        }
        return true;
    }

    private function updateGameStatus()
    {
        switch ($this->getGame()->status) {
            case Game::STATUS_QUESTION:
                if ($this->isRoundEnded()) {
                    app(Game::class)->updateStatus($this->getGame(), Game::STATUS_ANSWER);
                }
                break;
            case Game::STATUS_ANSWER:
                if (app(Game::class)->hasTimeElapsedFromLastUpdate($this->getGame(), 2)) {
                    app(Game::class)->updateStatus($this->getGame(), Game::STATUS_RESULT);
                }
                break;
            case Game::STATUS_RESULT:
                if ($this->allPlayersReady()) {
                    app(Game::class)->nextRound($this->getGame());
                }
                break;
        }
    }

    public function execute()
    {
        $this->updateGameStatus();
        $this->setQuestion();
        $this->setResult();

        $this->data['hash'] = md5(json_encode($this->data));
        return app(\App\Response\Json::class)->setJson($this->data);
    }
}

