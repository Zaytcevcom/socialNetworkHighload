<?php

declare(strict_types=1);

namespace App\Http\Exception;

use DomainException;
use Throwable;

final class DomainExceptionModule extends DomainException
{
    private string $module;
    private ?array $payload;

    public function __construct(
        string $module,
        string $message = '',
        int $code = 0,
        ?array $payload = null,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->module = $module;
        $this->payload = $payload;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }
}
