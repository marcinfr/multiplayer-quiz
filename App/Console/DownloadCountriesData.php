<?php

class DownloadCountriesData
{
    public $command = 'download-countries-data';

    public function execute()
    {
        echo 'Reading wikipedia' . "\n";
        $html = file_get_contents("https://pl.wikipedia.org/wiki/Lista_pa%C5%84stw_%C5%9Bwiata");
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);
        
        $rows = $xpath->query('//table[contains(@class, "wikitable")][1]//tr');

        $countryData = [];

        $mediaDir = app(\App\Models\Quiz::class)->getImagesDirPath() . 'countries';
        if (!file_exists($mediaDir)) {
            mkdir($mediaDir);
        }

        foreach ($rows as $i => $row) {
            $cols = $row->getElementsByTagName('td');
            if (count($cols) > 4) {
                $data = [
                    'country' => trim($cols[1]->textContent),
                    'continent' => trim($cols[3]->textContent),
                    'capitol' => trim($cols[4]->textContent),
                ];

                echo 'Reading about ' . $data['country'] . ' on wikipedia' . "\n";

                $img = $cols[1]->getElementsByTagName('img')->item(0);
                $flagUrl = $img->getAttribute('src');
                if (strpos($flagUrl, '//') === 0) {
                    $flagUrl = 'https:' . $flagUrl;
                }
                $flagUrl = str_replace(['/40px-', '/20px-'], '/300px-', $flagUrl);
                echo 'Download flag: ' . $flagUrl ."\n";
                $image = app(\App\Models\Quiz::class)->saveImage($flagUrl, 'countries/flag_' . $i . '.jpg');
                $data['flag'] = $image['path'];

                $img = $cols[2]->getElementsByTagName('img')->item(0);
                $mapUrl = $img->getAttribute('src');
                if (strpos($mapUrl, '//') === 0) {
                    $mapUrl = 'https:' . $mapUrl;
                }
                $mapUrl = str_replace('/60px-', '/300px-', $mapUrl);
                echo 'Download map: ' . $mapUrl ."\n";

                $image = app(\App\Models\Quiz::class)->saveImage($mapUrl, 'countries/map_' . $i . '.jpg');
                $data['map'] = $image['path'];

                $countryData[] = $data;
            }
        }
        app(\App\Models\Quiz::class)->saveQuestions("countries", $countryData);
    }
}