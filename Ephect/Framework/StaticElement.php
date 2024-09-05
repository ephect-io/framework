<?php

namespace Ephect\Framework;

use Ephect\Framework\Logger\Logger;

class StaticElement
{
    protected static object|null $instance = null;
    protected static Logger|null $logger = null;

    protected function __construct()
    {
    }

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            self::$logger = Logger::create();
        }
        return self::$logger;
    }

    public static function create(...$params)
    {
        $class = __CLASS__;
        self::$instance = null;

        if (count($params) > 0) {
            self::$instance = new $class();
        } else {
            self::$instance = new $class($params);
        }

        return self::$instance;
    }
}
