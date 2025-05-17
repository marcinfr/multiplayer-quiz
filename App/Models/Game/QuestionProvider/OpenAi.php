<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

class OpenAi
{
    public $label = "AI";

    public $defaultPriority = 20;

    public function getOptionsBlock(array $params = [])
    {
        return false;
    }

    public function getQuestion($options)
    {
        $openAI = app(\App\Models\OpenAi::class);
        $questions = (array) $this->game->config->questions;

        $providerId = 'question_list';
        $questionProviders = app(\App\Models\Game::class)->getQuestionProviders();
        $questionProvider = $questionProviders[$providerId];
        $exampleQuestions = [];
        for ($i =0; $i < 3; $i ++) {
            $exampleQuestions[] = $questionProvider->getQuestion((array) $questions[$providerId]);
        }

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

        $pattern = '/```json(.*?)```/s';
        if (preg_match($pattern, $result, $matches)) {
            $json = trim($matches[1]);
        }

        $question = json_decode($json, true);
        $question['question'] = 'AI: ' . $question['question'];
        $answers = $question['answers'];
        shuffle($answers);
        $question['answers'] = $answers;

        return json_encode($question);
    }
}