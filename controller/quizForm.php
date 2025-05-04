<?php
require_once("model/quiz.php");

$id = $_GET["id"] ?? '';
$name = Quiz::getNameById($id);
?>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="pub/css/quiz.css">
    </head>
    <body>
        <div class="container">
            <a href="quizList" class="back">Wróć</a>
            <form method="post" action="saveQuiz">
                <input type="hidden" name="id" value="<?= $id ?>" />
                <div class="row">
                    <div class="col-25 text-right">
                        <label for="name">Nazwa</label>
                    </div>
                    <div class="col-75">
                        <input type="text" id="name" name="name" value="<?= $name ?>" placeholder="Nazwa Quizu ..." required>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="Zapisz">
                </div>
            </form>
            <?php if ($id): ?>
                <div class="row">
                    <div class="col-25 text-right">
                        <label>Ilość pytań</label>
                    </div>
                    <div class="col-75">
                    <label><?= Quiz::getQuestionsCount($id) ?></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25 text-right">
                    <a href="questionForm?quiz=<?= $id ?>" class="primary">Dodaj pytanie</a>
                    </div>
                    <div class="col-75 text-left">
                        <a href="questionList?quiz=<?= $id ?>" class="primary">Pokaż Pytania</a>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </body>
</html>