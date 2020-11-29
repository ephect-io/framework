<?php

namespace FunCom\CLI;

use FunCom\Core\Application as CoreApplication;

class Application extends CoreApplication
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
