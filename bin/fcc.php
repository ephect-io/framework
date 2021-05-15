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
        $argv = $params[0];

        $arg1 = !isset($argv[1]) ? null : $argv[1];
        if ($arg1 === '-c') {
            $this->compile();
        }

        if ($arg1 === '-s') {
            $this->sample();
        }
    }

    public function sample(): void
    {
        $sample = FRAMEWORK_ROOT . 'sample';

        Utils::safeMkDir(SRC_ROOT);
        $destDir = realpath(SRC_ROOT);

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }

        $tree = Utils::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }

    public function compile(): void
    {
        if (file_exists(CACHE_DIR)) {
            Utils::delTree(CACHE_DIR);
        }
        $compiler = new Compiler;
        $compiler->perform();
        $compiler->postPerform();
        $compiler->followRoutes();
        $compiler->purgeCopies();
    }
}

Program::main($argv, $argc);
