<?php

namespace Ephect\Commands\InstallPlugin;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Plugins\PluginInstaller;

class Lib extends AbstractCommandLib
{
    public function install(string $workingDirectory, bool $remove): void
    {
        $pluginInstaller = new PluginInstaller($workingDirectory);
        if ($remove) {
            $pluginInstaller->remove();
        } else {
            $pluginInstaller->install();
        }
    }
}

