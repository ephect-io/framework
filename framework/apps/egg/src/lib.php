<?php

namespace Ephect\Apps\Egg;

use Ephect\CLI\Application;
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

        $this->appDirectory = $parent->getDirectory();
        $this->appName = $parent->getName();
    }

    /**
     * Deletes recursively a tree of directories containing files.
     * It is a workaround for rmdir which doesn't allow the deletion
     * of directories not empty.
     *
     * @param string $path Top directory of the tree
     * @return boolean TRUE if deletion succeeds otherwise FALSE
     */
    private function _deltree($path)
    {
        $class_func = array(__CLASS__, __FUNCTION__);
        return is_file($path) ?
        @unlink($path) :
        array_map($class_func, glob($path . './*')) == @rmdir($path);
    }

    /**
     * Create the skeleton of the application
     */
    public function createTree()
    {
        $this->parent->writeLine("Current directory %s", __DIR__);

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
        $this->parent->writeLine("Current directory %s", $this->appDirectory);

        rsort($this->directories);
        foreach ($this->directories as $directory) {
            $dir = APP_CWD . $directory;
            if (file_exists($dir)) {
                $this->parent->writeLine("Removing directory %s", $dir);
                // $this->_deltree($dir);
                Utils::safeRmdir($dir);
            } else {
                $this->parent->writeLine("Cannot find directory %s", $dir);
            }
        }
    }
}
