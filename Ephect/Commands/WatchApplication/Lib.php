<?php

namespace Ephect\Commands\WatchApplication;

use Ephect\Framework\CLI\FileSystem\Watcher;
use Ephect\Framework\Commands\AbstractCommandLib;

class Lib extends AbstractCommandLib
{
    public function watch(): void
    {
        $watcher = new Watcher;

        $watcher->watch(SRC_ROOT, ['phtml', 'php']);
    }
}

