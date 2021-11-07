<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "sample")]
#[CommandDeclaration(desc: "Create the sample application tree.")]
class CreateSample extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->sample();
    }
}
