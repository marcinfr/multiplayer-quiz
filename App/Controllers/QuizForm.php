<?php

namespace App\Controllers;

class QuizForm extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $id = $this->getRequest()->getParam("id");
        $name = \App\Models\Quiz::getNameById($id);
        $content = new \App\Block\Template('quiz/form.phtml',
            [
                'id' => $id,
                'name' => $name,
            ]
        );
        app(\App\Block\Page::class)->addChild($content, 'content');
        return app(\App\Response\Page::class);
    }
}