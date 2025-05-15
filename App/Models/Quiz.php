<?php

namespace App\Models;

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

    public function getImagesDirPath()
    {
        return ROOT_PATH . '/media/';
    }

	public function saveQuestion(
		int $quizId,
		int $id,
		array $data
	) {
		if (self::quizExists($quizId)) {
			$questions = self::getQuestions($quizId);
			if (!isset($questions[$id])) {
				$id = max(0, array_key_last($questions));
				$id ++;
				$currentImage = null;
			} else {
				$currentImage = $questions[$id]['question_image'] ?? null;
			}

			if ($data['question_image'] !== ($currentImage['url'] ?? null)) {
				$data['question_image'] = $this->saveImage($data['question_image']);
                $this->removeImage($currentImage);
			} else {
				$data['question_image'] = $currentImage;
			}

			$questions[$id] = $data;
			$this->saveQuestions($quizId, $questions);
		}
	}

	public function saveQuestions($quizId, $questions)
	{
		$quizFile = self::quizDir . '/' . $quizId . '.json';
		$questions = json_encode($questions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if (!file_put_contents($quizFile, $questions)) {
			die("Nie udało się zapisać");
		}
	}

    public function removeImage($image)
    {
        $path = $image['path'] ?? false;
        if ($path) {
            @unlink($this->getImagesDirPath() . $path);
        }
    }

	public function saveImage($url, $fileName = null)
	{
		if ($url) {
			if (!$fileName) {
				$fileName = md5($url) . time() . '.jpg';
			}

			$options = [
    			'http' => [
        			'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n"
    			]
			];
			$context = stream_context_create($options);
			$img = file_get_contents($url, false, $context);

			if (!$img) {
				throw new \Exception("Nie udało się pobrać " . $url);
			}
			$sourceImage = \imagecreatefromstring($img);
    		if ($sourceImage === false) {
        		throw new \Exception("Nie udało się utworzyć obrazu.");
    		}

			$maxWidth = 300;
			$maxHeight = 300;

			$origWidth = \imagesx($sourceImage);
    		$origHeight = \imagesy($sourceImage);

			$aspectRatio = $origWidth / $origHeight;
    		$newWidth = $maxWidth;
    		$newHeight = $maxWidth / $aspectRatio;

    		if ($newHeight > $maxHeight) {
        		$newHeight = $maxHeight;
        		$newWidth = $maxHeight * $aspectRatio;
    		}

			$newWidth = round($newWidth);
			$newHeight = round($newHeight);

			$resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    		\imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    		\imagejpeg($resizedImage, $this->getImagesDirPath() . $fileName, 90);
   	 		\imagedestroy($sourceImage);
    		\imagedestroy($resizedImage);

			return [
				'url' => $url,
				'path' => $fileName,
			];
		}
	}
}