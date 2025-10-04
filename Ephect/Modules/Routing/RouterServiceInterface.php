<?php

namespace Ephect\Modules\Routing;

interface RouterServiceInterface
{
    public static function findRouteArguments(string $route): ?array;

    public static function findRouteQueryString(string $route): ?string;

    public function findRoute(string &$html): void;

    public function renderRoute(bool $pageFound, string $path, array $query, int $error, int $responseCode, array $middlewares, string &$html): void;

    public function doRouting(): ?array;

    public function addRoute(RouteInterface $route): void;

    public function saveRoutes(): bool;

    public function matchRoute(RouteEntity $route): ?array;
}
