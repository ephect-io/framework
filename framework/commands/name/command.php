<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "name")]
#[CommandDeclaration(desc: "Display the running application name.")]
class Name extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getName();
        $this->application->writeLine($data);
    }
}
