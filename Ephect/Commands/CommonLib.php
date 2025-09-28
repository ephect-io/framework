<?php

namespace Ephect\Commands;

use Ephect\Framework\CLI\Application;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Element;
use Ephect\Framework\Utils\File;

class CommonLib extends Element
{

    /**
     * Constructor
     */
    public function __construct(Application $parent)
    {
        parent::__construct($parent);
    }

    public function createCommonTrees(): void
    {
        $common = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Common';
        $src_dir = $common . DIRECTORY_SEPARATOR . 'config';

        File::safeMkDir(CONFIG_DIR);
        $destDir = realpath(CONFIG_DIR);

        $tree = File::walkTreeFiltered($src_dir);

        foreach ($tree as $filePath) {
            File::safeWrite($destDir . $filePath, '');
            copy($src_dir . $filePath, $destDir . $filePath);
        }

        $src_dir = $common . DIRECTORY_SEPARATOR . 'public';

        File::safeMkDir(CONFIG_DOCROOT);
        $destDir = realpath(CONFIG_DOCROOT);

        $tree = File::walkTreeFiltered($src_dir);

        foreach ($tree as $filePath) {
            File::safeWrite($destDir . $filePath, '');
            copy($src_dir . $filePath, $destDir . $filePath);
        }
    }

    public function requireTree(string $treePath): object
    {
        $tree = File::walkTreeFiltered($treePath, ['php']);
        $result = ['path' => $treePath, 'tree' => $tree];

        return (object)$result;
    }

    public function displayTree($path): void
    {
        $tree = File::walkTreeFiltered($path);
        Console::writeLine($tree);
    }


}
