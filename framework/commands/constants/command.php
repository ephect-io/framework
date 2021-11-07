<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "constants")]
#[CommandDeclaration(desc: "Display the application constants.")]
class Constants extends AbstractCommand
{

    public function run(): void
    {
        $this->application->displayConstants();
    }
}
