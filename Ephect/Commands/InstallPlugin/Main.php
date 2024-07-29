<?php

namespace Ephect\Commands\InstallPlugin;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "install", subject: "plugin")]
#[CommandDeclaration(desc: "Install an Ephect framework plugin.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $workingDirectory = $this->application->getArgi(2);
        $remove = $this->application->getArgi(3);

        $lib = new Lib($this->application);
        $lib->install($workingDirectory, $remove == '-r');

        return 0;
    }
}
