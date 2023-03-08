<?php

declare(strict_types=1);

namespace App\Components\SmsSender;

class SmsCRu implements SmsSender
{
    private string $login;
    private string $password;
    private string $sender;
    private string $url;

    public function __construct(string $login, string $password, string $sender, string $url = 'https://smsc.ru/sys/send.php')
    {
        $this->login = $login;
        $this->password = $password;
        $this->sender = $sender;
        $this->url = $url;
    }

    public function send(string $number, string $text): void
    {
        $url = $this->url
            . '?login=' . urlencode($this->login)
            . '&psw=' . urlencode($this->password)
            . '&phones=' . urlencode($number)
            . '&mes=' . urlencode($text)
            . '&sender=' . urlencode($this->sender)
            . '&fmt=3';

        file_get_contents($url);
    }
}
