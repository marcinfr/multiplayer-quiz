<?php

namespace App\Controllers;

use \App\Models\Quiz;

class SaveQuestion extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = (string) $this->getRequest()->getParam('quiz');
        $imgUrl = $this->getRequest()->getParam('question-image');

        $wrongAnswers = $this->getRequest()->getParam('wrong_answer');
        foreach($wrongAnswers as $id => $value) {
            if (!$value) {
                unset($wrongAnswers[$id]);
            }
        }

        app(Quiz::class)->saveQuestion(
            $quizId,
            (int) $this->getRequest()->getParam('id'),
            [
                'question' => $this->getRequest()->getParam('question'),
                'question_image' => $this->getRequest()->getParam('question-image'),
				'answer' => $this->getRequest()->getParam('answer'),
				'wrong_answers' => $wrongAnswers
            ]
        );

        return app(\App\Response\Redirect::class)->setUrl('quizForm?id=' . $quizId);
    }
}
