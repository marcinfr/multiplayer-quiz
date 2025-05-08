<?php

namespace App\Controllers;

class QuestionList extends \App\Controllers\AbstractController
{
    public function execute()
    {
        
    }
}
?>



<?php

use \App\Models\Quiz;

$quizId = $_GET["quiz"] ?? '';
$questions = Quiz::getQuestions($quizId);
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
    </head>
    <body>
        <div class="container">
            <a href="quizForm?id=<?= $quizId ?>" class="back">Wróć</a>
            <table>
                <?php foreach($questions as $id => $data): ?>
                    <tr>
                        <td><?= $data['question'] ?? '' ?></td>
                        <td><a href="questionForm?quiz=<?= $quizId ?>&id=<?= $id ?>" class="primary">Edytuj</a></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </body>
</html>