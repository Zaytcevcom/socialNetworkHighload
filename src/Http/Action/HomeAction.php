<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Components\FeatureToggle\FeatureFlag;
use App\Http\Response\JsonDataResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

final class HomeAction implements RequestHandlerInterface
{
    public function __construct(
        private readonly FeatureFlag $flag
    ) {
    }

    public function handle(Request $request): Response
    {
        if ($this->flag->isEnabled('IS_DEV')) {
            return new JsonDataResponse(['name' => 'API DEVELOP']);
        }

        return new JsonDataResponse(new stdClass());
    }
}
