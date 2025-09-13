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

        $player = $this->getCurrentPlayer();
        $question = app(Game::class)->getQuestion($this->getGame());

        foreach ($question['answers'] as $id => $answer) {
            $isCorrect = $answer['correct'] ?? false;
            if ($player->has_answer && $player->last_selected_answer == $id) {
                if ($this->getGame()->status == Game::STATUS_ANSWER) {
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

            if ($this->getGame()->status == Game::STATUS_ANSWER
                && ($question['show-suggested'] ?? false)
                && $player->has_answer
                && $isCorrect
                && $player->last_selected_answer != $id
            ) {
                $class .= ' suggested';
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
    }

    private function setResult()
    {
        $this->data['section-result'] = '';
        if ($this->getGame()->status == Game::STATUS_RESULT
            || $this->getGame()->status == Game::STATUS_WAITING_FOR_QUESTION
        ) {
            if ($this->isGameEnded()) {
                $message = 'Gra zakończona!';
            } else {
                $message = 'Gotowy na następne pytanie?';
            }
            $resultBlock = new \App\Block\Template('game/result.phtml', [
                'game' => $this->getGame(),
                'player' => $this->getCurrentPlayer(),
                'players' => $this->getPlayers(),
                'is_game_ended' => $this->isGameEnded(),
                'message' => $message,
            ]);
            $this->data['section-result'] = $resultBlock->getHtml();
        }
    }

    private function isGameEnded()
    {
        return  app(Game::class)->isGameEnd($this->getGame());
    }

    private function isRoundEnded()
    {
        $hasPlayers = false;
        foreach ($this->getPlayers() as $player) {
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
                if ($this->allPlayersReady() && $this->getCurrentPlayer()->is_host) {
                    app(Game::class)->updateStatus($this->getGame(), Game::STATUS_WAITING_FOR_QUESTION);
                    //app(Game::class)->nextRound($this->getGame());
                    $command = '/usr/bin/php ' . ROOT_PATH . '/bin/console generate-question ' . (int) $this->getGame()->id;
                    exec($command . ' > /dev/null 2>&1 &');
                }
                break;
        }
    }

    public function execute()
    {
        /** init host player, only host player can generate questions */
        app(Game::class)->getHostPlayer($this->getGame());
        $player = $this->getCurrentPlayer();
        $player->last_activity_timestamp = time();
        app(Player::class)->update($player, ['last_activity_timestamp']);

        $this->updateGameStatus();
        $this->setQuestion();
        $this->setResult();

        $this->data['hash'] = md5(json_encode($this->data));
        return app(\App\Response\Json::class)->setJson($this->data);
    }
}

