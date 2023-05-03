<?php

declare(strict_types=1);

use App\Http\Action;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use ZayMedia\Shared\Components\Router\StaticRouteGroup as Group;

return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', Action\V1\OpenApiAction::class);

        $group->group('/identity', new Group(static function (RouteCollectorProxy $group): void {
            $group->post('/token', Action\V1\Identity\TokenAction::class);
            $group->get('/profile', Action\V1\Identity\GetProfileAction::class);
            $group->post('/signup', Action\V1\Identity\SignupAction::class);
        }));

        $group->group('/users', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('/search', Action\V1\Users\SearchAction::class);
            $group->get('/{id}', Action\V1\Users\GetByIdAction::class);
            $group->post('/friends/{id}', Action\V1\Friends\AddAction::class);
            $group->delete('/friends/{id}', Action\V1\Friends\RemoveAction::class);
        }));

        $group->group('/posts', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('/feed', Action\V1\Posts\GetFeedAction::class);
            $group->get('/{id}', Action\V1\Posts\GetByIdAction::class);
            $group->put('/{id}', Action\V1\Posts\UpdateAction::class);
            $group->post('', Action\V1\Posts\CreateAction::class);
            $group->delete('/{id}', Action\V1\Posts\DeleteAction::class);
        }));
    }));
};
