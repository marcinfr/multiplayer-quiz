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
        if (random_int(1, 100) > $aiPercent) {
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
        for ($i =0; $i < 3; $i ++) {
            $exampleQuestions[] = $this->getDefinedQuestion($options);
        }

        $question = $exampleQuestions[0];

        $userPrompt = "Stwórz jedno pytanie do quizu podaj je w formacie json, tutaj przykładowe pytania, na ich podstawie stworz inne: "
            . implode(',', $exampleQuestions);

        $result = $openAI->prompt(
            [
                "model" => 'gpt-4o-mini',
                "messages" => [
                    ["role" => "user", "content" => $userPrompt]
                ],
            ]
        );

        if ($result) {
            $pattern = '/```json(.*?)```/s';
            if (preg_match($pattern, $result, $matches)) {
                $json = trim($matches[1]);
            }

            $question = json_decode($json, true);
            $question['question'] = '[AI] ' . $question['question'];
            $answers = $question['answers'];
            shuffle($answers);
            $question['answers'] = $answers;
        }

        return json_encode($question);
    }
}
