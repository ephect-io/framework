<?php

namespace Ephect\Apps\Egg;

use Ephect\CLI\Application;
use Ephect\Components\Compiler;
use Ephect\Element;
use Ephect\IO\Utils;

class EggLib extends Element
{

    /**
     * Defines the full directory tree of a Ephect web application
     */
    private $directories = [
        'app',
        'app/business',
        'app/controllers',
        'app/models',
        'app/rest',
        'app/scripts',
        'app/templates',
        'app/views',
        'app/webservices',
        'cache',
        'cert',
        'config',
        'css',
        'data',
        'docker',
        'fonts',
        'logs',
        'media',
        'media/images',
        'runtime',
        'themes',
        'tmp',
        'tools',
        'web',
        'web/css',
        'web/css/images',
        'web/fonts',
        'web/js',
        'web/js/runtime',
        'web/media',
        'web/media/images',
    ];

    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);

    }

    /**
     * Create the skeleton of the application
     */
    public function createTree()
    {
        $this->parent->writeLine("Current directory %s", APP_CWD);

        sort($this->directories);
        foreach ($this->directories as $directory) {
            if (!file_exists($directory)) {
                $this->parent->writeLine("Creating directory %s", $directory);
                mkdir($directory, 0755, true);
            } else {
                $this->parent->writeLine("Directory %s already exist", $directory);
            }
        }
    }

    /**
     * Deletes recursively all known directories of the application
     */
    public function deleteTree()
    {
        $this->parent->writeLine("Current directory %s", APP_CWD);

        rsort($this->directories);
        foreach ($this->directories as $directory) {
            $dir = APP_CWD . $directory;
            if (file_exists($dir)) {
                $this->parent->writeLine("Removing directory %s", $dir);
                Utils::safeRmdir($dir);
            } else {
                $this->parent->writeLine("Cannot find directory %s", $dir);
            }
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
