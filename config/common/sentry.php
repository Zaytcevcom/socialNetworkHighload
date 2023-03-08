<?php

declare(strict_types=1);

use App\Components\Sentry;
use Sentry\SentrySdk;

return [
    Sentry::class => static fn (): Sentry => new Sentry(SentrySdk::getCurrentHub()),
];
