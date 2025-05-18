<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

class OpenAi extends QuestionLists
{
    public $label = "Ai";
    public $defaultPriority = 20;

    public function getQuestion($options)
    {
        $openAI = app(\App\Models\OpenAi::class);
        $exampleQuestions = [];
        for ($i =0; $i < 3; $i ++) {
            $exampleQuestions[] = parent::getQuestion($options);
        }

        $question = $exampleQuestions[0];

        $userPrompt = "Stwórz jedno pytanie do quizu podaj je w formacie json, tutaj przykładowe pytania, 
        na ich podstawie stworz zupełnie inne, 
        aby sie nie powtarzalo z moimi ale bylo o podobnym poziomi trudnosci. 
        Musi być tylko jedna poprawna odpowiedz. Odpowiedzi nie mogą się powtarzać.
        napisz samego jsona, nic więcej: "
            . implode(',', $exampleQuestions);

        $result = $openAI->prompt(
            [
                "model" => 'gpt-4.1',
                "messages" => [
                    ["role" => "user", "content" => $userPrompt]
                ],
            ]
        );

        if ($result) {
            try {
                $aiQuestion = json_decode($result, true);
                if ($this->validateQuestion($aiQuestion)) {
                    $aiQuestion['question'] = '[AI] ' . $aiQuestion['question'];
                    $answers = $aiQuestion['answers'];
                    shuffle($answers);
                    $aiQuestion['answers'] = $answers;
                    return json_encode($aiQuestion);
                }
            } catch (\Exception $e) {
                // do nothing
            }
        }

        return $question;
    }

    protected function validateQuestion($question)
    {
        if (!isset($question['question'])) {
            return false;
        }

        if (!$question['question'] || !is_string($question['question'])) {
            return false;
        }

        if (!isset($question['answers']) || !is_array($question['answers']) || count($question['answers']) != 4) {
            return false;
        }

        $correctAnswers = 0;
        foreach ($question['answers'] as $answer) {
            $isCorrect = $answer['correct'] ?? false;
            if ($isCorrect) {
                $correctAnswers ++;
            }
            if (!isset($answer['answer']) || !$answer['answer'] || !is_string($answer['answer'])) {
                return false;
            }
        }

        if ($correctAnswers != 1) {
            return false;
        }

        return true;
    }
}
