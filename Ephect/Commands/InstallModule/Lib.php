<?php

namespace Ephect\Commands\InstallModule;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Modules\ModuleInstaller;

class Lib extends AbstractCommandLib
{
    public function install(string $workingDirectory, bool $remove): void
    {
        $moduleInstaller = new ModuleInstaller($workingDirectory);
        if ($remove) {
            $moduleInstaller->remove();
        } else {
            $moduleInstaller->install();
        }
    }
}

