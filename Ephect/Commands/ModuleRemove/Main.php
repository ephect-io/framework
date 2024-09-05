<?php

namespace Ephect\Commands\ModuleRemove;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "module", subject: "remove")]
#[CommandDeclaration(desc: "Composer remove wrapper and more.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $package = $this->application->getArgi(2);
        $version = $this->application->getArgi(3);

        $lib = new Lib($this->application);
        $lib->remove($package, $version);

        return 0;
    }
}
