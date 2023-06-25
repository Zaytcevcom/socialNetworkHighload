<?php

declare(strict_types=1);

namespace App\Http\Action;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use ZayMedia\Shared\Http\Response\HtmlResponse;

final class MetricsAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly CollectorRegistry $registry
    ) {
    }

    public function handle(Request $request): Response
    {
        $data = (new RenderTextFormat())
            ->render($this->registry->getMetricFamilySamples());

        return new HtmlResponse($data);
    }
}
