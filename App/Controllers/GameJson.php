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

    private function setGameInfo()
    {
        if ($this->getGame()->round < 1) {
            if ($this->getCurrentPlayer()->is_host) {
                $this->data['start'] = [];
            } else {
                $this->data['wait'] = [];
            }
        }
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

    private function getPlayersListHtml()
    {
        $html = '';
        foreach($this->getPlayers() as $player) {
            $html .= '<tr><td>' . $player->name . '<td></tr>';
        }
        return $html;
    }

    
    private function setPlayersInfo()
    {
        if ($this->getGame()->round < 1) {
            $playersHtml = $this->getPlayersListHtml($this->getGame());
            if ($playersHtml) {
                $this->data['players']['list'] = $playersHtml;
            }
        }
    }

    private function setQuestion()
    {
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
        $this->data['question']['question-number'] = $this->getGame()->round;
        $this->data['question']['question-text'] = $question['question'];
        $this->data['answers']['answers-list'] = '';
        $answersListHtml = '';
        foreach ($question['answers'] as $id => $answer) {
            $id += 1;
            $class = 'answer';
            if ($id == $this->getCurrentPlayer()->last_selected_answer) {
                if ($this->getGame()->status == Game::STATUS_ANSWER) {
                    $isCorrect = $answer['correct'] ?? false;
                    if ($isCorrect) {
                        $class .= ' correct';
                    } else {
                        $class .= ' incorrect';
                    }
                } else {
                    $class .= ' selected';
                }
            } else {
                $class .= ' not-selected';
            }
            $this->data['answers']['answers-list'] .= '<div class="' . $class. '" data-index="' . $id. '">' . $answer['answer'] . '</div>';
        }
    }

    private function setRoundResult()
    {
        if ($this->getGame()->status != Game::STATUS_ROUND_RESULT) {
            return;
        }
        $this->data['round-result'] = [];
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
                    app(Game::class)->updateStatus($this->getGame(), Game::STATUS_ROUND_RESULT);
                }
                break;
            case Game::STATUS_ROUND_RESULT:
                if ($this->allPlayersReady()) {
                    app(Game::class)->nextRound($this->getGame());
                }
                break;
        }
    }

    public function execute()
    {
        $this->updateGameStatus();
        $this->setGameInfo();
        $this->setPlayersInfo();
        $this->setQuestion();
        $this->setRoundResult();

        $this->data['hash'] = md5(json_encode($this->data));
        echo json_encode($this->data);
    }
}

