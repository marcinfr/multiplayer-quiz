<?php

require_once('model/quiz.php');

$quizId = $_POST['quiz'];

Quiz::saveQuestion(
    (int) $quizId,
    (int) $_POST['id'],
    $_POST['question'],
    $_POST['answer'],
    $_POST['wrong_answer_1'],
    $_POST['wrong_answer_2'],
    $_POST['wrong_answer_3'],
);

header("Location: quizForm?id=" . $quizId);