<?php

namespace App\Controllers\Game;

use App\Models\Game;
use App\Models\Player;

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
        $player = $this->getCurrentPlayer();

        foreach ($question['answers'] as $id => $answer) {
            if ($player->has_answer && $player->last_selected_answer == $id) {
                if ($this->getGame()->status == Game::STATUS_ANSWER) {
                    $isCorrect = $answer['correct'] ?? false;
                    if ($isCorrect) {
                        $class= 'correct';
                    } else {
                        $class = 'incorrect';
                    }
                } else {
                    $class = ' selected';
                }
            } else {
                $class = 'not-selected';
            }
            $question['answers'][$id]['class'] = $class;
        }

        if ($this->getCurrentPlayer()->view_type == Player::VIEW_TYPE_CONTROLLER) {
            $showQuestion = false;
        } else {
            $showQuestion = true;
        }

        if ($this->getCurrentPlayer()->view_type == Player::VIEW_TYPE_HOST) {
            $showAnswers = false;
        } else {
            $showAnswers = true;
        }
        
        $questionBlock =  new \App\Block\Template('game/question.phtml', [
            'game' => $this->getGame(),
            'question' => $question,
            'show_question' => $showQuestion,
            'show_answers' => $showAnswers,
        ]);

        $this->data['section-question'] = $questionBlock->getHtml();

        return;
    }

    private function setResult()
    {
        $this->data['section-result'] = '';
        if ($this->getGame()->status == Game::STATUS_RESULT) {
            $resultBlock = new \App\Block\Template('game/result.phtml', [
                'game' => $this->getGame(),
                'player' => $this->getCurrentPlayer(),
                'players' => $this->getPlayers(),
            ]);
            $this->data['section-result'] = $resultBlock->getHtml();
        }
    }

    private function isRoundEnded()
    {
        $hasPlayers = false;
        foreach ($this->getPLayers() as $player) {
            if (!$player->has_answer) {
                return false;
            }
            $hasPlayers = true;
        }
        return $hasPlayers;
    }

    private function allPlayersReady()
    {
        $hasPlayers = false;
        foreach($this->getPlayers() as $player) {
            if ($player->has_answer) {
                return false;
            }
            $hasPlayers = true;
        }
        return $hasPlayers;
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

