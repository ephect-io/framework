<?php

namespace Ephect\Modules\Routing\Services;

use Ephect\Modules\Routing\Base\RouteInterface;
use Ephect\Modules\Routing\Entities\RouteEntity;

interface RouterServiceInterface
{
    public static function findRouteArguments(array $routeItem): ?array;

    public static function findRouteQueryString(array $item): ?string;

    public function findRoute(string &$html): void;

    public function renderRoute(bool $pageFound, string $path, array $query, int $error, int $responseCode, array $middlewares, string &$html): void;

    public function doRouting(): ?array;

    public function addRoute(RouteInterface $route): void;

    public function saveRoutes(): bool;

    public function matchRoute(RouteEntity $route): ?array;
}
