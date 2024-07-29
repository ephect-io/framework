<?php

namespace Ephect\Commands\InstallPlugin;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Components\PluginInstaller;

class Lib extends AbstractCommandLib
{
    public function install(string $workingDirectory, bool $remove): void
    {
        if($remove) {
            PluginInstaller::remove($workingDirectory);
        } else {
            PluginInstaller::install($workingDirectory);
        }
    }
}

