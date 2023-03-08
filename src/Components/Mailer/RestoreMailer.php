<?php

declare(strict_types=1);

namespace App\Components\Mailer;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;

class RestoreMailer implements RestoreMailerInterface
{
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        // TODO: Implement send() method.
    }
}
