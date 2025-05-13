<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

use App\Models\Quiz;

class QuestionLists
{
    public function getQuestion($game)
    {
        $quizIds = $game->config->quiz_ids ?? [];
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
}
