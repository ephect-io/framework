<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "compile", short: "c")]
#[CommandDeclaration(desc: "Compile the application.")]
class CreateSample extends AbstractAttributedCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->compile();
    }
}
