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
