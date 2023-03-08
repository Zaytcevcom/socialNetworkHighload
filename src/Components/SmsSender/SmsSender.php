<?php

declare(strict_types=1);

namespace App\Components\SmsSender;

interface SmsSender
{
    public function send(string $number, string $text): void;
}
