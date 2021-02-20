<?php

namespace Ephect\CLI;

use Ephect\Core\AbstractApplication;

class Application extends AbstractApplication
{
    public static function create(...$params): void
    {
        self::$instance = new Application();
        self::$instance->run($params);
    }

    public function run(?array ...$params) : void
    {
    }
}
