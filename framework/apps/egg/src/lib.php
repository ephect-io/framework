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

    
    public function requireMaster(): object
    {
        $result = [];

        $libRoot = $this->appDirectory . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;

        if (!file_exists($libRoot)) {
            mkdir($libRoot);
        }

        $master = $libRoot . 'master';
        $filename = $master . '.zip';
        $ephectDir = $master . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR;

        $tree = [];

        if (!file_exists($filename)) {
            $this->parent->writeLine('Downloading ephect github master');
            $curl = new Curl();
            $result = $curl->request('https://codeload.github.com/CodePhoenixOrg/ephect/zip/master');
            file_put_contents($filename, $result->content);
        }

        if (file_exists($filename)) {
            $this->parent->writeLine('Inflating ephect master archive');
            $zip = new Zip();
            $zip->inflate($filename);
        }

        if (file_exists($master)) {
            $tree = Utils::walkTree($ephectDir, ['php']);
        }

        $result = ['path' => $ephectDir, 'tree' => $tree];

        return (object) $result;
    }

    public function requireTree(string $treePath): object
    {
        $result = [];

        $tree = Utils::walkTreeFiltered($treePath, ['php']);

        $result = ['path' => $treePath, 'tree' => $tree];

        return (object) $result;
    }

    public function displayEphectTree(): void
    {
        // $tree = [];
        // \ephect\Utils\TFileUtils::walkTree(EPHECT_ROOT, $tree);
        $tree = Utils::walkTree(EPHECT_ROOT);

        $this->parent->writeLine($tree);
    }

    public function displayTree($path): void
    {
        $tree = Utils::walkTree($path);
        $this->parent->writeLine($tree);
    }
}
