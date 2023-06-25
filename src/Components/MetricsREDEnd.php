<?php

declare(strict_types=1);

namespace App\Components;

use Prometheus\CollectorRegistry;
use Prometheus\Exception\MetricsRegistrationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MetricsREDEnd implements MiddlewareInterface
{
    private string $attr = 'startTime';

    public function __construct(
        private readonly CollectorRegistry $registry,
        private readonly string $namespace = 'test'
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->setRate();

            $response = $handler->handle($request);
            if ($response->getStatusCode() >= 400) {
                $this->setErrors();
            }

            $this->setDuration($request);
        } catch (\Exception) {
        }

        return $handler->handle(
            $request->withoutAttribute($this->attr)
        );
    }

    /** @throws MetricsRegistrationException */
    private function setRate(): void
    {
        $requestRate = $this->registry->getOrRegisterCounter(
            namespace: $this->namespace,
            name: 'http_requests_total',
            help: 'The total number of HTTP requests'
        );

        $requestRate->inc();
    }

    /** @throws MetricsRegistrationException */
    private function setErrors(): void
    {
        $errorRate = $this->registry->getOrRegisterCounter(
            namespace: $this->namespace,
            name: 'http_errors_total',
            help: 'The total number of HTTP errors'
        );

        $errorRate->inc();
    }

    /** @throws MetricsRegistrationException */
    private function setDuration(ServerRequestInterface $request): void
    {
        $requestDuration = $this->registry->getOrRegisterHistogram(
            namespace: $this->namespace,
            name: 'http_request_duration_seconds',
            help: 'The HTTP request duration in seconds',
            labels: ['method', 'path'],
            buckets: [0.1, 0.2, 0.5, 1, 2, 5]
        );

        $startTime = $request->getAttribute($this->attr);

        if (!is_numeric($startTime)) {
            return;
        }

        $duration = microtime(true) - $startTime;

        $requestDuration->observe(
            value: $duration,
            labels: [
                'method' => $request->getMethod(),
                'path'   => (string)$request->getUri(),
            ]
        );
    }
}
