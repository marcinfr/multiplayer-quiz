<?php

namespace App\Controllers;

use \App\Models\Quiz;

class SaveQuestion extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $quizId = $this->getRequest()->getParam('quiz');

        Quiz::saveQuestion(
            (int) $quizId,
            (int) $this->getRequest()->getParam('id'),
            $_POST['question'],
            $_POST['answer'],
            $_POST['wrong_answer_1'],
            $_POST['wrong_answer_2'],
            $_POST['wrong_answer_3'],
        );

        header("Location: quizForm?id=" . $quizId);
    }
}
