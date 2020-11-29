<?php
namespace FunCom\Core;

include  dirname(__DIR__) . '/vendor/autoload.php';

use FunCom\CLI\Application;
use FunCom\Components\Compiler;

class Program extends Application
{
    public static function main($argv, $argc) {
 
        $compiler = new Compiler;

        $compiler->perform();

    }

}

Program::main($argv, $argc);