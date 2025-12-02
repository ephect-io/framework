<?php

namespace Ephect\Modules\ParallelBridge;

use Ephect\Framework\Modules\ModuleBootstrapInterface;


class Bootstrap implements ModuleBootstrapInterface
{
    public function boot(): void
    {
        if(!class_exists('parallel')) {
            throw new \Exception("The Parallel extension is not installed or enabled. Please install/enable it to use the ParallelBridge module.");
        }
    }
}
