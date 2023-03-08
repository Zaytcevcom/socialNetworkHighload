<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Exception\UnauthorizedHttpException;
use App\Http\Response\JsonErrorResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class UnauthorizedHttpExceptionHandler implements MiddlewareInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (UnauthorizedHttpException $exception) {
            $this->logger->warning($exception->getMessage(), [
                'code' => $exception->getCode(),
                'exception' => $exception,
                'url' => (string)$request->getUri(),
            ]);

            return new JsonErrorResponse(
                code: $exception->getCode(),
                message: $this->translator->trans('error.unauthorized', [], 'oauth'),
                status: $exception->getCode()
            );
        }
    }
}
