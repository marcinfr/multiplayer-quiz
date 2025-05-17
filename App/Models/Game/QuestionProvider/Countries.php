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
        $countriesIds = array_rand($data, 4);
        $countries = [];
        foreach ($countriesIds as $id) {
            $countries[] = $data[$id];
        }

        $correctCountry = array_shift($countries);

        $type = array_rand($types);
        switch ($types[$type]) {
            case 'capitol':
                $question = [
                    'question' => 'Stolica kraju' . $correctCountry['country'] . ' to?',
                    'answers' => $this->getAnswers($countries, $correctCountry, 'capitol'),
                ];
                break;
            case 'map':
                $question = [
                    'question' => 'Które to państwo?',
                    'question_image' => $correctCountry['map'],
                    'answers' => $this->getAnswers($countries, $correctCountry),
                ];
                break;
            case 'flag':
                $question = [
                    'question' => 'Którego państwa jest ta flaga?',
                    'question_image' => $correctCountry['flag'],
                    'answers' => $this->getAnswers($countries, $correctCountry),
                ];
                break;
        }

        return json_encode($question);
    }

    protected function getAnswers($countries, $correctCountry, $field = 'country')
    {
        $answers = [];
        $answers[] = [
            'answer' => $correctCountry[$field],
            'correct' => true,
        ];
        foreach($countries as $country) {
            $answers[] = [
                'answer' => $country[$field],
            ];
        }

        shuffle($answers);
        return $answers;
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