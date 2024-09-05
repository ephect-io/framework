<?php

namespace Ephect\Framework\Registry;

class RouteRegistry extends AbstractStaticRegistry
{
    private static ?RegistryInterface $instance = null;

    public static function addMiddleware(string $middleware): void
    {
        self::getInstance()->_addMiddleware($middleware);
    }

    public function _addMiddleware(string $middleware): void
    {
        $this->_write('middlewares', $middleware);
    }

    public static function reset(): void
    {
        self::$instance = new RouteRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): RegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new RouteRegistry;
        }

        return self::$instance;
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

    public static function getMovedFilename(): string
    {
        return CACHE_DIR . 'routes.json';
    }

    public static function getMovedPhpFilename(): string
    {
        return CACHE_DIR . 'routes.php';
    }
}
