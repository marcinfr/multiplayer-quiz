<?php

namespace App\Controllers;

class QuizList extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $list = \App\Models\Quiz::getList();
        $template = new \App\Block\Template('quiz/list.phtml',
            [
                'list' => $list,
            ]
        );
        $template->render();
    }
}
