<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "build")]
#[CommandDeclaration(desc: "Build the application.")]
class BuildApplication extends AbstractCommand
{
    public function run(): void
    {

        $egg = new EggLib($this->application);
        $egg->build();
    }
}
