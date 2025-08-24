<?php

namespace App\Models\Game\QuestionProvider;

abstract class AbstractQuestionProvider
{
    public $defaultPriority = 100;

    private $providerCode;
    private $game;

    abstract public function getQuestion($options);

    public function setGame($game)
    {
        $this->game = $game;
        return $this;
    }

    public function setProviderCode(string $providerCode)
    {
        $this->providerCode = $providerCode;
        return $this;
    }

    public function getExcludedQuestions()
    {
        if ($this->game && $this->providerCode) {
            $excludedQuestions = $this->game->questions_history[$this->providerCode] ?? [];
            return array_reverse($excludedQuestions);
        }
        return [];
    }

    public function unExcludeQuestion()
    {
        if ($this->game) {
            array_shift($this->game->questions_history[$this->providerCode]);
        }
        return $this;
    }

    public function addExcludedQuestion($questionCode, $maxExcludedQuestions = null)
    {
        if ($this->game) {
            if ($maxExcludedQuestions && count($this->getExcludedQuestions()) >= $maxExcludedQuestions) {
                $this->unExcludeQuestion();
            }
            $this->game->questions_history[$this->providerCode][] = $questionCode;
        }
        return $this;
    }
}