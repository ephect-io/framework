<?php
namespace FunCom\Core;

use FunCom\Components\Compiler;

class Application
{
    public static function run() {
 
        $compiler = new Compiler;

        $compiler->perform();

        

    }

}