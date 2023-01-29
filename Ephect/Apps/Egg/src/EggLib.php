<?php

namespace Ephect\Apps\Egg;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\System\Command;
use Ephect\Framework\Components\FileSystem\Watcher;
use Ephect\Framework\Core\Builder;
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

        Utils::safeMkDir(CONFIG_DOCROOT);
        $destDir = realpath(CONFIG_DOCROOT);

        $tree = Utils::walkTreeFiltered($src_dir);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($src_dir . $filePath, $destDir . $filePath);
        }
    }

    public function watch(): void
    {
        $watcher = new Watcher;

        $watcher->watch(SRC_ROOT, ['phtml', 'php']);
        
    }

    public function requireMaster(): object
    {
        $result = [];

        $libRoot = CACHE_DIR . 'archive' . DIRECTORY_SEPARATOR;

        Console::writeLine($libRoot);

        if (!file_exists($libRoot)) {
            mkdir($libRoot);
        }

        $master = $libRoot . 'main';
        $filename = $master . '.zip';
        $ephectDir = $master . DIRECTORY_SEPARATOR . 'framework-main' . DIRECTORY_SEPARATOR . 'Ephect' . DIRECTORY_SEPARATOR . 'Framework' . DIRECTORY_SEPARATOR;

        $tree = [];

        if (!file_exists($filename)) {
            Console::writeLine('Downloading ephect github main');
            $curl = new Curl();
            [$code, $header, $content] = $curl->request('https://codeload.github.com/ephect-io/framework/zip/main');

            file_put_contents($filename, $content);
        }

        if (file_exists($filename)) {
            Console::writeLine('Inflating ephect master archive');
            $zip = new Zip();
            $zip->inflate($filename);
        }

        if (file_exists($filename)) {
            $tree = Utils::walkTreeFiltered($ephectDir, ['php']);
        }

        unlink($filename);
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

        Console::writeLine($tree);
    }

    public function displayTree($path): void
    {
        $tree = Utils::walkTreeFiltered($path);
        Console::writeLine($tree);
    }

    public function getPort($default = 8000): int
    {

        $port = $default;

        if ($this->parent->getArgc() > 2) {
            $customPort = $this->parent->getArgv()[2];

            $cleanPort = preg_replace('/([\d]+)/', '$1', $customPort);

            if ($cleanPort !== $customPort) {
                $customPort = $port;
            }

            $port = $customPort;
        }

        return $port;
    }
  
}
