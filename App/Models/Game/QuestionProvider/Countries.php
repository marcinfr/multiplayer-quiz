<?php

namespace App\Models\Game\QuestionProvider;

class Countries
{
    public $label = "Państwa Świata";

    public function getOptionsBlock()
    {
        return false;
    }

    public function getQuestion($game)
    {
        $data = file_get_contents(\App\Models\Quiz::quizDir . '/countries.json');
        $data = json_decode($data, true);
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

        $question = [
            'question' => 'Którego państwa jest ta flaga:',
            'question_image' =>  $correctCountry['flag'],
            'answers' => $answers,
        ];

        return json_encode($question);
    }
}