<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "compile")]
#[CommandDeclaration(desc: "Compile the application.")]
class CompileApplication extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->compile();
    }
}
