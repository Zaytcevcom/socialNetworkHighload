<?php

declare(strict_types=1);

namespace App\Components\Router;

use DomainException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class Route
{
    public static function getArgument(ServerRequestInterface $request, string $name): string
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $argument = $route?->getArgument($name);

        if (null === $argument) {
            throw new DomainException();
        }

        return $argument;
    }

    public static function getArgumentToInt(ServerRequestInterface $request, string $name): int
    {
        return (int)self::getArgument($request, $name);
    }
}
