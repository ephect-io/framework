<?php

namespace Ephect\Commands\ComposerRequire;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "composer", subject: "require")]
#[CommandDeclaration(desc: "Composer require wrapper")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $package = $this->application->getArgi(2);
        $version = $this->application->getArgi(3);

        $lib = new Lib($this->application);
        $lib->require($package, $version);

        return 0;
    }
}
