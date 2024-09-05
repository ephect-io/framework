<?php

namespace Ephect\Commands\WatchApplication;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Components\FileSystem\Watcher;

class Lib extends AbstractCommandLib
{
    public function watch(): void
    {
        $watcher = new Watcher;

        $watcher->watch(SRC_ROOT, ['phtml', 'php']);
    }
}

