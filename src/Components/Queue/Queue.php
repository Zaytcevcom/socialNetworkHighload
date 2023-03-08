<?php

declare(strict_types=1);

namespace App\Components\Queue;

interface Queue
{
    public function send(string $queue, array|string $message): void;

    public function receive(string $queue, callable $callback): void;
}
