<?php

namespace App\Controllers;

class QuestionForm extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = $this->getRequest()->getParam("quiz");
        $id = $this->getRequest()->getParam("id");
        $question = \App\Models\Quiz::getQuestion($quizId, $id);
        $wrongAnswers = $question['wrong_answers'] ?? [];
        $wrongAnswers = array_pad($wrongAnswers, 3, '');
        $question['wrong_answers'] = $wrongAnswers;

        $content = new \App\Block\Template('question/form.phtml',
            [
                'quiz_id' => $quizId,
                'question_id' => $id,
                'question' => $question,
            ]
        );
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}
