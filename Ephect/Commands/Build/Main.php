<?php

namespace Ephect\Commands\Build;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "build")]
#[CommandDeclaration(desc: "Build the application.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $egg = new Lib($this->application);
        $egg->build();

        return 0;
    }
}
