<?php

namespace Ephect\Modules\Routing\Registry;

use Ephect\Framework\Registry\AbstractStaticRegistry;
use Ephect\Framework\Registry\RegistryInterface;

use function Ephect\Hooks\useMemory;

class RouteRegistry extends AbstractStaticRegistry
{
    private static ?RouteRegistry $instance = null;

    public static function addMiddleware(string $middleware): void
    {
        self::getInstance()->__addMiddleware($middleware);
    }

    public function __addMiddleware(string $middleware): void
    {
        $this->__write('middlewares', $middleware);
    }

    public static function getInstance(): RouteRegistry
    {
        if (self::$instance === null) {
            self::$instance = new RouteRegistry();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = new RouteRegistry();
        unlink(self::$instance->getCacheFilename());
    }

    public static function getCachedRoutes(): ?array
    {
        $result = null;

        if (self::hasMoved()) {
            $result = require self::getMovedPhpFilename();
        }

        return $result;
    }

    public static function hasMoved(): bool
    {
        return file_exists(self::getMovedPhpFilename());
    }

    public static function getMovedPhpFilename(): string
    {
        [$buildDirectory] = useMemory(get: 'buildDirectory');
        return $buildDirectory . 'routes.php';
    }

    public static function getMovedFilename(): string
    {
        [$buildDirectory] = useMemory(get: 'buildDirectory');
        return $buildDirectory . 'routes.json';
    }
}
