<?php

use \App\Models\Quiz;

$data = Quiz::getList();

$id = $_POST['id'];
    if (!Quiz::quizExists($id)) {
    $id = array_key_last($data);
    $id ++;
}

$name = $_POST['name'];
$data[$id] = $name;

$data = json_encode($data);

if (!file_put_contents(Quiz::listFile, $data)) {
    die("Nie udało się zapisać");
}

header("Location: quizForm?id=" . $id);
