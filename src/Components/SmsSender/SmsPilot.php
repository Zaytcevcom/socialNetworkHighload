<?php

declare(strict_types=1);

namespace App\Components\SmsSender;

class SmsPilot implements SmsSender
{
    private string $apiKey;
    private string $sender;
    private string $url;

    public function __construct(string $apiKey, string $sender, string $url = 'https://smspilot.ru/api.php')
    {
        $this->apiKey = $apiKey;
        $this->sender = $sender;
        $this->url = $url;
    }

    public function send(string $number, string $text): void
    {
        $url = $this->url
            . '?send=' . urlencode($text)
            . '&to=' . urlencode($number)
            . '&from=' . $this->sender
            . '&apikey=' . $this->apiKey
            . '&format=json';

        file_get_contents($url);
    }
}
