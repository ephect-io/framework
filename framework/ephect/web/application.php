<?php

namespace Ephect\Web;

use Ephect\Components\Compiler;
use Ephect\Components\Component;
use Ephect\Core\AbstractApplication;
use Ephect\Registry\CacheRegistry;
use Ephect\Registry\ComponentRegistry;
use Ephect\Registry\PluginRegistry;

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
        if(!ComponentRegistry::uncache()) {
            $compiler = new Compiler;
            $compiler->perform();
            $compiler->postPerform();
        }
        if(!CacheRegistry::uncache()) {
            PluginRegistry::uncache();            
        }

        Component::render('App');
    }
}
