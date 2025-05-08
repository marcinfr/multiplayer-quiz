<?php

namespace App\Controllers;

class QuizList extends \App\Controllers\AbstractController
{
    public function execute()
    {
        
    }
}

use \App\Models\Quiz;

$list = Quiz::getList();
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
    </head>
    <body>
        <div class="container">
            <a href="." class="back">Wróć</a>
            <a href="quizForm" class="primary">Nowy Quiz</a>
            <table>
                <?php foreach($list as $id => $name): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><a href="quizForm?id=<?= $id ?>" class="primary">Edytuj</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </body>
</html>