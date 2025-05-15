<?php

class DownloadCountriesData
{
    public $command = 'download-coutries-data';

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

                echo 'Reading about ' . $data['country'] . ' on wikipaedia' . "\n";

                $link = $cols[1]->getElementsByTagName('a')->item(0);
                if ($link) {
                    $href = $link->getAttribute('href');
                    $fullUrl = 'https://pl.wikipedia.org' . $href;
                    $details = $this->getCountryDetails($fullUrl);
                    if ($details['flag_url']) {
                        echo 'Download flag: ' . $details['flag_url'] ."\n";
                        $image = app(\App\Models\Quiz::class)->saveImage($details['flag_url'], 'countries/flag_' . $i . '.jpg');
                        $details['flag'] = $image['path'];
                    }
                    unset($details['flag_url']);
                }

                $data = array_merge($data, $details);
                $countryData[] = $data;
            }
        }
        app(\App\Models\Quiz::class)->saveQuestions("countries", $countryData);
    }

    protected function getCountryDetails($url)
    {
        $details = [
            'flag_url' => '',
        ];
        $html = file_get_contents($url);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new DOMXPath($doc);

        $img = $xpath->query('//img[@alt="Flaga"]')->item(0);

        $flagUrl = null;
        if ($img) {
            $src = $img->getAttribute('src');

            $src = str_replace('/120px-', '/300px-', $src);

            if (strpos($src, '//') === 0) {
                $src = 'https:' . $src;
            }

           $details['flag_url'] = $src;
        }

        return $details;
    }
}