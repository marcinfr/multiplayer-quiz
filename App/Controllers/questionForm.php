<?php

use \App\Models\Quiz;

$quizId = $_GET["quiz"];
$id = $_GET['id'] ?? '';
$question = Quiz::getQuestion($quizId, $id);
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
    </head>
    <body>
        <div class="container">
            <a href="quizForm?id=<?= $quizId ?>" class=back>Wróć</a>
            <form method="post" action="saveQuestion">
                <input type="hidden" name="quiz" value="<?= $quizId ?>" />
                <input type="hidden" name="id" value="<?= $id ?>" />
                <div class="row">
                    <div class="col-25 text-right">
                        <label for="question">Pytanie</label>
                    </div>
                    <div class="col-75">
                        <textarea id="question" name="question" required placeholder="Pytanie.." style="height:200px"><?= $question['question'] ?? '' ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25 text-right">
                        <label for="answer">Poprawna Odpowiedź</label>
                    </div>
                    <div class="col-75">
                        <textarea id="answer" name="answer" required placeholder="Poprawna Odpowiedź.." style="height:200px"><?= $question['answer'] ?? '' ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25 text-right">
                        <label for="answer">Błędne Odpowiedzi</label>
                    </div>
                    <div class="col-75">
                        <textarea id="wrong_answer_1" name="wrong_answer_1" required placeholder="Błędna Odpowiedź.." style="height:200px"><?= $question['wrong_answers'][0] ?? '' ?></textarea>
                        <br/>
                        <textarea id="wrong_answer_2" name="wrong_answer_2" required placeholder="Błędna Odpowiedź.." style="height:200px"><?= $question['wrong_answers'][1] ?? '' ?></textarea>
                        <br/>
                        <textarea id="wrong_answer_2" name="wrong_answer_3" required placeholder="Błędna Odpowiedź.." style="height:200px"><?= $question['wrong_answers'][2] ?? '' ?></textarea>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="Zapisz">
                </div>
            </form>
        </div>
    </body>
</html>