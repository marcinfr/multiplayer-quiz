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
            $id = max(0, $data);
            $id ++;
        }

        $name = $this->getRequest()->getParam('name');;
        $data[$id] = $name;

        $data = json_encode($data);

        if (!file_put_contents(Quiz::listFile, $data)) {
            die("Nie udało się zapisać");
        }

        header("Location: quizForm?id=" . $id);
    }
}

