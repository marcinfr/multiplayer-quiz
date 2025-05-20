<?php

namespace App\Controllers;

use \App\Models\Quiz;

class SaveQuiz extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $data = Quiz::getList();

        $id = $this->getRequest()->getParam('id');
        if (!Quiz::quizExists($id)) {
            $id = 0;
            foreach ($data as $quizId => $label) {
                if (is_numeric($quizId) && $quizId > $lastId) {
                    $id = $quizId;
                }
            }
            $id ++;
        }

        $name = $this->getRequest()->getParam('name');
        $data[$id] = $name;

        $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (!file_put_contents(Quiz::listFile, $data)) {
            die("Nie udało się zapisać");
        }

        return app(\App\Response\Redirect::class)->setUrl('quizForm?id=' . $id);
    }
}

