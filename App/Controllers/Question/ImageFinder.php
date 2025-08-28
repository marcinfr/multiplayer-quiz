<?php

namespace App\Controllers\Question;

class ImageFinder extends \App\Controllers\AbstractController
{
    public function execute()
    {
        $config = config();
        $googleConfig = $config['GOOGLE'];
        $apiKey = $googleConfig['API_KEY'];
        $searchEngineId = $googleConfig['SEARCH_ENGINE_ID'];

        // 🔍 Fraza do wyszukania (np. z formularza)
        $query = $this->getRequest()->getParam('query');
        // Endpoint Google Custom Search
        $url = "https://www.googleapis.com/customsearch/v1"
            . "?key=" . urlencode($apiKey)
            . "&cx=" . urlencode($searchEngineId)
            . "&q=" . urlencode($query)
            . "&searchType=image" // ważne -> szuka tylko obrazków
            . "&num=10"; // liczba wyników (max 10 na żądanie)

        // Pobranie wyników
        $response = file_get_contents($url);

        $result = [
            'items' => []
        ];

        if ($response == FALSE) {
            $result['error'] = "Błąd w zapytaniu do Google API";
        } else {
            $data = json_decode($response, true);
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    if ($item['fileFormat'] != 'image/jpeg') {
                        continue;
                    }
                    if ($item['image']['width'] < 300) {
                        continue;
                    }
                    if ($item['image']['height'] < 300) {
                        continue;
                    }
                    $result['items'][] = [
                        'url' => $item['link'],
                        'thumbnail' => $item['image']['thumbnailLink'],
                    ];
                }
            }
        }

        if (empty($result['items'])) {
            $result['error'] = "Nic nie znalazlem";
        }

        return app(\App\Response\Json::class)->setJson($result);
    }
}