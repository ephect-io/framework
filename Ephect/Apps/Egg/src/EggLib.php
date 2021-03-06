<?php

namespace Ephect\Apps\Egg;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\Components\Compiler;
use Ephect\Framework\Element;
use Ephect\Framework\IO\Utils;
use Ephect\Framework\Utils\Zip;
use Ephect\Framework\Web\Curl;

class EggLib extends Element
{

    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);

    }

    public function createQuickstart(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'QuickStart';

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

    public function createSkeleton(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Skeleton';

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
    
    public function createCommonTrees(): void
    {
        $common = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Common';
        $src_dir = $common . DIRECTORY_SEPARATOR . 'config';

        Utils::safeMkDir(CONFIG_DIR);
        $destDir = realpath(CONFIG_DIR);

        $tree = Utils::walkTreeFiltered($src_dir);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($src_dir . $filePath, $destDir . $filePath);
        }

        $src_dir = $common . DIRECTORY_SEPARATOR . 'public';

        Utils::safeMkDir(PUBLIC_DIR);
        $destDir = realpath(PUBLIC_DIR);

        $tree = Utils::walkTreeFiltered($src_dir);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($src_dir . $filePath, $destDir . $filePath);
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
        
        // $compiler->performAgain();
        $compiler->followRoutes();
        // Compiler::purgeCopies();
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
        $ephectDir = $master . DIRECTORY_SEPARATOR . 'ephect-master' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'ephect' . DIRECTORY_SEPARATOR;

        $tree = [];

        if (!file_exists($filename)) {
            $this->parent->writeLine('Downloading ephect github main');
            $curl = new Curl();
            $result = $curl->request('https://codeload.github.com/ephect-io/framework/zip/main');
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
