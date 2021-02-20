<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Ephect\CLI\Application;
use Ephect\Components\Compiler;
use Ephect\IO\Utils;

class Program extends Application
{
    public static function main($argv, $argc)
    {
        
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {
        if(file_exists(CACHE_DIR)) {
            Utils::delTree(CACHE_DIR);
        }
        $compiler = new Compiler;
        $compiler->perform();
    }
}

Program::main($argv, $argc);
