<?php

namespace FunCom\Web;

use FunCom\Components\Compiler;
use FunCom\Components\View;
use FunCom\Core\AbstractApplication;
use FunCom\Registry\CacheRegistry;

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
        if(!CacheRegistry::uncache()) {
            $compiler = new Compiler;
            $compiler->perform();
        }

        View::render('App');
    }
}
