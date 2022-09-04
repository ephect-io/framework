<?php

namespace Ephect\Framework\Registry;

class RouteRegistry extends AbstractStaticRegistry
{
    private static $instance = null;

    public static function reset(): void {
        self::$instance = new RouteRegistry;
        unlink(self::$instance->getCacheFilename());
    }

    public static function getInstance(): AbstractRegistryInterface
    {
        if (self::$instance === null) {
            self::$instance = new RouteRegistry;
        }

        return self::$instance;
    }

    public static function getCachedRoutes(): ?array
    {
        $result = null;

        if(file_exists(CACHE_DIR . 'routes.json')) {
            $json = file_get_contents(CACHE_DIR . 'routes.json');
            $result = json_decode($json, JSON_OBJECT_AS_ARRAY);
        }

        return $result;
    }
}
