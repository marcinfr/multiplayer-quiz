<?php

namespace App\Controllers;

class QuizList extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $list = \App\Models\Quiz::getList();
        $content = new \App\Block\Template('quiz/list.phtml',
            [
                'list' => $list,
            ]
        );
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}
