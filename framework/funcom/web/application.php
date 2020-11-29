<?php

namespace FunCom\Web;

use FunCom\Components\Compiler;
use FunCom\Components\Parser;
use FunCom\Core\Application as CoreApplication;
use FunCom\IO\Utils;
use FunCom\Registry\ClassRegistry;

class Application extends CoreApplication
{

    public static function create(...$params): void
    {
        session_start();
        self::$instance = new Application();
        self::$instance->run($params);
    }

    public function run(?array ...$params): void
    {
        ClassRegistry::uncache();
        $classes =  ClassRegistry::items();

        $appFunction = '';
        $appFilename = '';

        foreach($classes as $fqName => $filename) {
            $appFunction = $fqName;
            $appFilename = SITE_ROOT . $filename;
        break;            
        }
       
        include $appFilename;

        //list($ns, $fn, $code) = Parser::getFunctionDefinition($appFilename);

        $html = call_user_func($appFunction);

        echo $html;
    }
}
