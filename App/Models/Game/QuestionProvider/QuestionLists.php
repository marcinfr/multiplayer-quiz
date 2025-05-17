<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

use App\Models\Quiz;

class QuestionLists
{
    public $label = "Listy Pytań";
    public $defaultPriority = 100;

    public function getOptionsBlock(array $params = [])
    {
        return new \App\Block\Template('game/form/questionLists.phtml', $params);
    }

    public function getQuestion($options)
    {
        $aiPercent = $options['ai'] ?? 0;
        if (random_int(1, 100) < $aiPercent) {
            return $this->getAiQuestion($options);
        } else {
            return $this->getDefinedQuestion($options);
        }
    }

    protected function getDefinedQuestion($options)
    {
        $quizIds = $options['options'] ?? [];
        $questions = [];
        foreach ($quizIds as $quizId) {
            $quizQesitions = Quiz::getQuestions($quizId);
            $questions = array_merge($questions, $quizQesitions);
        }

        $randomQuestionId = array_rand($questions);
        $randomQuestion = $questions[$randomQuestionId];
        $answers = [[
            'answer' => $randomQuestion['answer'],
            'correct' => true,
        ]];
        $wrongAnswers = $randomQuestion['wrong_answers'] ?? [];
        foreach ($wrongAnswers as $answer) {
            $answers[] = [
                'answer' => $answer
            ];
        }

        shuffle($answers);

        if (isset($randomQuestion['question_image'])) {
            $questionImage = $randomQuestion['question_image']['path'];
        }

        return json_encode([
            'question' => $randomQuestion['question'],
            'question_image' => $questionImage ?? null,
            'answers' => $answers,
        ]);
    }

    protected function getAiQuestion($options)
    {
        $openAI = app(\App\Models\OpenAi::class);
        $exampleQuestions = [];
        for ($i =0; $i < 5; $i ++) {
            $exampleQuestions[] = $this->getDefinedQuestion($options);
        }

        $question = $exampleQuestions[0];

        $userPrompt = "Stwórz jedno pytanie do quizu podaj je w formacie json, tutaj przykładowe pytania, na ich podstawie stworz inne. napisz samego jsona, nic więcej: "
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
