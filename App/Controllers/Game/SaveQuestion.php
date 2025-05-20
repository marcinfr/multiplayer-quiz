<?php

namespace App\Controllers\Game;

use \App\Models\Player;
use \App\Models\Game;
use \App\Models\Quiz;

class SaveQuestion extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $player = app(Player::class)->getCurrentPlayer();
        $game = app(Game::class)->getByPlayer($player);
        $question  = json_decode($game->current_question, true);
        $answers = $question['answers'];

        $quizId = $question['save_as'] ?? false;

        if ($quizId) {

            unset($question['save_as']);
            $game->current_question = json_encode($question);
            app(Game::class)->update($game, ['current_question']);

            $wrongAnswers = [];
            $correctAnswer = '';

            foreach ($answers as $answer) {
                $isCorrect = $answer['correct'] ?? false;
                if ($isCorrect) {
                    $correctAnswer = $answer['answer'];
                } else {
                    $wrongAnswers[] = $answer['answer'];
                }
            }

            app(Quiz::class)->saveQuestion(
                $quizId,
                0,
                [
                    'question' => $question['question'],
                    'question_image' => $question['question_image'] ?? null,
                    'answer' => $correctAnswer,
                    'wrong_answers' => $wrongAnswers
                ]
            );
        }
    }
}