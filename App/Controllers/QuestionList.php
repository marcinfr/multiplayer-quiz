<?php

namespace App\Controllers;

class QuestionList extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = $this->getRequest()->getParam("quiz");
        $template = new \App\Block\Template('question/list.phtml',
            [
                'quiz_id' => $quizId,
                'questions' => \App\Models\Quiz::getQuestions($quizId),
            ]
        );
        $template->render();
    }
}
