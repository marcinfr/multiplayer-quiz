<?php

class Quiz
{
	const quizDir = "quiz";
    const listFile = "quiz/list.json";

    public static function getList()
    {
		$data = [];
		if (file_exists(self::listFile)) {
	    	$data = file_get_contents(self::listFile);
	    	$data = json_decode($data, true);
		}
		return $data;
    }

    public static function getNameById($id)
    {
		$list = self::getList();
		return $list[$id] ?? ''; 
    }

	public static function getQuestionsCount($quizId)
	{
		$questions = self::getQuestions($quizId);
		return count($questions);
	}

	public static function quizExists($id)
	{
		$list = self::getList();
		return isset($list[$id]);
	}

	public static function getQuestions($quizId)
	{
		$data = [];
		$quizFile = self::quizDir . '/' . $quizId . '.json';
		if (file_exists($quizFile)) {
			$data = file_get_contents($quizFile);
			$data = json_decode($data, true);
		}
		return $data;
	}

	public static function getQuestion($quizId, $questionId)
	{
		$questions = self::getQuestions($quizId);
		return $questions[$questionId] ?? [];
	}

	public static function saveQuestion(
		int $quizId,
		int $id,
		string $questuion,
		string $answer,
		string $wrongAnswer1,
		string $wrongAnswer2,
		string $wrongAnswer3
	) {
		if (self::quizExists($quizId)) {
			$questions = self::getQuestions($quizId);
			if (!isset($questions[$id])) {
				$id = max(0, array_key_last($questions));
				$id ++;
			}
			$questions[$id] = [
				'question' => $questuion,
				'answer' => $answer,
				'wrong_answers' => [
					$wrongAnswer1,
					$wrongAnswer2,
					$wrongAnswer3
				]
			];
			$quizFile = self::quizDir . '/' . $quizId . '.json';
			$questions = json_encode($questions);
			if (!file_put_contents($quizFile, $questions)) {
    			die("Nie udało się zapisać");
			}
		}
	}
}