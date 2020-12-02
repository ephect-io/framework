<?php

namespace FunCom\Web;

use FunCom\Components\View;
use FunCom\Core\AbstractApplication;

class Application extends AbstractApplication
{

    public static function create(...$params): void
    {
        session_start();
        self::$instance = new Application();
        self::$instance->run($params);
    }

    public function run(?array ...$params): void
    {
        View::render('App');
    }
}
