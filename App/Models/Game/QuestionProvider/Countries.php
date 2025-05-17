<?php

namespace App\Models\Game\QuestionProvider;

class Countries
{
    public $label = "Państwa Świata";
    public $defaultPriority = 10;

    public function getOptionsBlock(array $params = [])
    {
        $params['options'] = $this->getQeustionTypes();
        return new \App\Block\Template('game/form/countries.phtml', $params);
    }

    public function getQeustionTypes()
    {
        return [
            'map' => 'Mapy',
            'flag' => 'Flagi',
            'capitol' => 'Stolice'
        ];
    }

    public function getQuestion($options)
    {
        $types = $options['options'] ?? [];
        $data = file_get_contents(\App\Models\Quiz::quizDir . '/countries.json');
        $data = json_decode($data, true);
        shuffle($data);
        $countries = array_rand($data, 4);

        $correctCountryId = array_shift($countries);
        $correctCountry = $data[$correctCountryId];

        $answers = [];
        $answers[] = [
            'answer' => $correctCountry['country'],
            'correct' => true,
        ];
        foreach($countries as $country) {
            $answers[] = [
                'answer' => $data[$country]['country'],
            ];
        }

        shuffle($answers);

        $question = $this->getCountryQuestion($correctCountry, $types);

        $question['answers'] = $answers;

        return json_encode($question);
    }

    protected function getCountryQuestion($correctCountry, $types)
    {
        $type = array_rand($types);
        switch ($types[$type]) {
            case 'capitol':
                return [
                    'question' => 'Którego państwa stolicą jest ' . $correctCountry['capitol'] . '?',
                ];
            case 'map':
                return [
                    'question' => 'Które to państwo?',
                    'question_image' => $correctCountry['map'],
                ];
            case 'flag':
                return [
                    'question' => 'Którego państwa jest ta flaga?',
                    'question_image' => $correctCountry['flag'],
                ];
        }
    }
}