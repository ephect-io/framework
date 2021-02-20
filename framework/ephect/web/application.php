<?php

namespace Ephect\Web;

use Ephect\Components\Compiler;
use Ephect\Components\View;
use Ephect\Core\AbstractApplication;
use Ephect\Registry\CacheRegistry;

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
