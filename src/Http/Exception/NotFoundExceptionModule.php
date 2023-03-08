<?php

declare(strict_types=1);

namespace App\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Throwable;

final class NotFoundExceptionModule extends HttpNotFoundException
{
    private string $module;

    public function __construct(
        string $module,
        ServerRequestInterface $request,
        string $message = '',
        Throwable $previous = null
    ) {
        parent::__construct($request, $message, $previous);
        $this->module = $module;
    }

    public function getModule(): string
    {
        return $this->module;
    }
}
