<?php

namespace Ephect\Commands\BuildWebcomponent;

class Lib
{
    public function watch(): void
    {
        $watcher = new Watcher;

        $watcher->watch(SRC_ROOT, ['phtml', 'php']);
    }
}

