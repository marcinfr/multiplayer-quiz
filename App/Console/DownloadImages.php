<?php

use \App\Models\Quiz;

class DownloadImages
{
    public $command = 'download-images';

    public function execute()
    {
        foreach (app(Quiz::class)->getList() as $quizId => $name) {
            $quizQestions = Quiz::getQuestions($quizId);
            $updated = false;
            foreach ($quizQestions as $questionId => $question) {
                $image = $question['question_image'] ?? false;;
                if ($image) {
                    $url = $image['url'];
                    $fileName = $image['path'];
                    $path = app(Quiz::class)->getImagesDirPath() . $fileName;
                    if ($url && !file_exists($path)) {
                        try {
                            echo 'Downloading : ' . $url . "\n";
                            $quizQestions[$questionId]['question_image'] = app(Quiz::class)->saveImage($url, $fileName);
                            if (!$fileName) {
                                $updated = true;
                            }
                        } catch (\Exception $e) {
                            echo 'Error' . $e->getMessage();
                        }
                    }
                }
            }
            if ($updated) {
                app(Quiz::class)->saveQuestions($quizId, $quizQestions);
            }
        }
    }
}