<?php

namespace Ephect\Core;

use Ephect\Apps\Egg\EggLib;
use Ephect\CLI\Application;
use Ephect\CLI\PharInterface;
use Ephect\Components\Compiler;
use Ephect\IO\Utils;

include 'includes.php';
include 'lib.php';

class Program extends Application implements PharInterface
{

    public static function main($argv, $argc)
    {
        (new Program($argc, $argv));
    }

    public function __construct($argc, $argv)
    {
        parent::__construct($argv, $argc);
    }

    public function ignite(): void {
        parent::ignite();

        $egg = new EggLib($this);

        $this->setCommand(
            'create',
            '',
            'Create the application tree.',
            function () use ($egg) {
                $egg->createTree();
            }
        );            

        $this->setCommand(
            'delete',
            '',
            'Delete the application tree.',
            function () use($egg) {
                $egg->deleteTree();
            }
        );

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
