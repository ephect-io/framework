<?php

namespace Ephect\Core;

use Ephect\CLI\Application;
use Ephect\CLI\PharInterface;
use Ephect\Components\Compiler;
use Ephect\IO\Utils;

include \Phar::running() ? 'ephect_library.php' : dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'bootstrap.php';

class Program extends Application implements PharInterface
{

    public static function main($argv, $argc)
    {
        (new Program($argc, $argv));
    }

    public function __construct($argc, $argv)
    {
        $dir = dirname(__FILE__);
        parent::__construct($argv, $argc, $dir);
    }

    public function ignite(): void {
        parent::ignite();

        $this->setCommand(
            'compile',
            'c',
            'compile all components of the application so they are readable by PHP processor',
            function () {
                $this->compile();
            }
        );

        $this->setCommand(
            'sample',
            's',
            'create the sample application',
            function () {
                $this->sample();
            }
        );

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
