<?php

namespace App\Controllers;

class QuestionForm extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = $this->getRequest()->getParam("quiz");
        $id = $this->getRequest()->getParam("id");
        $question = \App\Models\Quiz::getQuestion($quizId, $id);
        $template = new \App\Block\Template('question/form.phtml',
            [
                'quiz_id' => $quizId,
                'question_id' => $id,
                'question' => $question,
            ]
        );
        $template->render();
    }
}
