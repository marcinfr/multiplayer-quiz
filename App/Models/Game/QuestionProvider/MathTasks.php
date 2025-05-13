<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */

namespace App\Models\Game\QuestionProvider;

class MathTasks
{
    public function getQuestion($game)
    {
        $questionTypes = ['getPlus', 'getMinus', 'getMultiplication', 'getDivision'];
        $question = $questionTypes[array_rand($questionTypes)] . 'Question';
        return json_encode($this->$question());
    }

    protected function getPlusQuestion()
    {
        $x = rand(0, 100);
        $y = rand(0, 100);
        $correct = $x + $y;
        return [
            'question' => $x . ' + ' . $y . ' = ?',
            'answers' => $this->getAnswers($correct),
        ];
    }

    protected function getMinusQuestion()
    {
        $x = rand(0, 100);
        $y = rand($x, 100);
        $correct = $y - $x;
        return [
            'question' => $y . ' - ' . $x . ' = ?',
            'answers' => $this->getAnswers($correct),
        ];
    }

    protected function getMultiplicationQuestion()
    {
        $x = rand(0, 10);
        $y = rand(0, 10);
        $correct = $y * $x;
        return [
            'question' => $y . ' * ' . $x . ' = ?',
            'answers' => $this->getAnswers($correct),
        ];
    }

    protected function getDivisionQuestion()
    {
        $y = rand(0, 10);
        $correct = rand(0, 10);
        $x = $y * $correct;
        return [
            'question' => $x . ' / ' . $y . ' = ?',
            'answers' => $this->getAnswers($correct),
        ];
    }

    protected function getAnswers($correct)
    {
        $answers = [
            $correct => [
                'answer' => $correct,
                'correct' => true,
            ]
        ];
        $wrongAnswerCount = 0;
        do {
            $wrongAnswer = rand(max(0, $correct), $correct + 20);
            if (!isset($answers[$wrongAnswer])) {
                $answers[$wrongAnswer] = [
                    'answer' => $wrongAnswer,
                ];
                $wrongAnswerCount++;
            }
        } while ($wrongAnswerCount < 3);
        $answers = array_values($answers);
        shuffle($answers);
        return $answers;
    }
}