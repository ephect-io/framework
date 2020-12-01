<?php

namespace FunCom\Web;

use FunCom\Components\Compiler;
use FunCom\Components\Parser;
use FunCom\Components\View;
use FunCom\Core\AbstractApplication;
use FunCom\IO\Utils;
use FunCom\Registry\ClassRegistry;
use FunCom\Registry\UseRegistry;

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
