<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Models;

class OpenAi
{
    protected $url = "https://api.openai.com/v1/chat/completions";

    protected function getApiKey()
    {
        $config = config();
        return  $config['OPENAI_KEY'];
    }

    public function prompt(array $data = [])
    {
        if (!$this->getApiKey()) {
            return false;
        }

        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $this->url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->getApiKey()
        ];
        if ($headers) {
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        \curl_setopt($ch, CURLOPT_POST, 1);
        \curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = \curl_exec($ch);
        $error = \curl_errno($ch) ? curl_error($ch) : null;
        \curl_close($ch);

        if($error !== null) {
            return false;
        }

        $openaiResponse = json_decode($response, true);
        return $openaiResponse["choices"][0]["message"]["content"] ?? false;
    }
}