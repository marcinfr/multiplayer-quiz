<?php

namespace App\Controllers;

use \App\Models\Quiz;

class SaveQuestion extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = $this->getRequest()->getParam('quiz');
        $imgUrl = $this->getRequest()->getParam('question-image');


        app(Quiz::class)->saveQuestion(
            (int) $quizId,
            (int) $this->getRequest()->getParam('id'),
            [
                'question' => $this->getRequest()->getParam('question'),
                'question_image' => $this->getRequest()->getParam('question-image'),
				'answer' => $this->getRequest()->getParam('answer'),
				'wrong_answers' => [
					$this->getRequest()->getParam('wrong_answer_1'),
					$this->getRequest()->getParam('wrong_answer_2'),
					$this->getRequest()->getParam('wrong_answer_3'),
				]
            ]
        );

        return app(\App\Response\Redirect::class)->setUrl('quizForm?id=' . $quizId);
    }
}
