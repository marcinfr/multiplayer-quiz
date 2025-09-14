<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

use App\Models\Quiz;

class QuestionLists extends AbstractQuestionProvider
{
    const PROVIDER_CODE = 'question_list';

    public $label = "Listy Pytań";

    public function getOptionsBlock(array $params = [])
    {
        return new \App\Block\Template('game/form/questionLists.phtml', $params);
    }

    public function getQuestion($options)
    {
        $quizIds = $options['options'] ?? [];
        $questions = [];
        foreach ($quizIds as $quizId) {
            $quizQesitions = Quiz::getQuestions($quizId);
            foreach ($quizQesitions as $key => $question) {
                $questionCode = $quizId . '-' . $key;
                $questions[$questionCode] = $question;
            }
        }

        $this->excludeQuestions($questions);
        $randomQuestionCode = array_rand($questions);
        $randomQuestion = $questions[$randomQuestionCode];
        $answers = [[
            'answer' => $randomQuestion['answer'],
            'correct' => true,
        ]];
        $wrongAnswers = $randomQuestion['wrong_answers'] ?? [];
        shuffle($wrongAnswers);
        $wrongAnswers = array_slice($wrongAnswers, 0, 3);
        
        foreach ($wrongAnswers as $answer) {
            $answers[] = [
                'answer' => $answer
            ];
        }

        shuffle($answers);

        if (isset($randomQuestion['question_image'])) {
            $questionImage = $randomQuestion['question_image']['path'];
        }

        $this->addExcludedQuestion($randomQuestionCode, 100);

        return [
            'question' => $randomQuestion['question'],
            'question_image' => $questionImage ?? null,
            'answers' => $answers,
        ];
    }

    private function excludeQuestions(&$questions)
    {
        if (empty($questions)) {
            return $this;
        }
        $exculdedQuestion = $this->getExcludedQuestions();
        $minQuestion = ceil(count($questions) / 5);
        foreach ($exculdedQuestion as $questionCode) {
            if (count($questions) <= $minQuestion) {
                $this->unExcludeQuestion();
                break;
            }
            unset($questions[$questionCode]);
        }
        return $this;
    }

    public function validateConfig($config)
    {
        if (empty($config['options']) || !is_array($config['options'])) {
            return false;
        }
        return true;
    }
}
