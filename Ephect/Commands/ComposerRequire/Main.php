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
        $params = $this->application->getArgv();




        $lib = new Lib($this->application);
        $lib->composer($params);

        return 0;
    }
}
