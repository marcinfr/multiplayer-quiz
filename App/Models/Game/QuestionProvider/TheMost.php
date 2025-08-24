<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models\Game\QuestionProvider;

use App\Models\Quiz;

class TheMost extends AbstractQuestionProvider
{
    public $label = "Naj...";
    public $defaultPriority = 50;
    const listFile = "quiz/the-most-list.json";

    public function getOptionsBlock(array $params = [])
    {
        $params['topics'] = $this->getTopics();
        return new \App\Block\Template('game/form/theMost.phtml', $params);
    }

    public static function getTopics()
    {
        $data = [];
        if (file_exists(self::listFile)) {
            $data = file_get_contents(self::listFile);
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getQuestion($options)
    {
        $topics = $options['options'] ?? [];
        $topic = $topics[array_rand($topics)];

        $file = 'quiz/' . $topic . '.json';
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        $subtopic = $data[array_rand($data)];
        $questionId = array_rand($subtopic['questions']);
        $question = $subtopic['questions'][$questionId];
        $selectedItems = array_rand($subtopic['items'], 4);

        $answers = [];
        $correctAnswerId = 0;
        $mostValue = null;
        $i = 0;
        foreach ($selectedItems as $itemId) {
            $item = $subtopic['items'][$itemId];
            $answers[] = [
                'answer' => $item['label'],
            ];
            $value = (int) $item['value'];
            if ($questionId < 0) {
                $value *= -1;
            }
            if ($mostValue == null) {
                $mostValue = $value;
            } elseif ($value > $mostValue) {
                $mostValue = $value;
                $correctAnswerId = $i;
            }
            $i ++;
        }
        $answers[$correctAnswerId]['correct'] = true;

        return json_encode([
            'question' => $question,
            'show-suggested' => true,
            'answers' => $answers,
        ]);
    }
}
