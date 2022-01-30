<?php

namespace Ephect\Commands;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "constants")]
#[CommandDeclaration(desc: "Display the application constants.")]
class Constants extends AbstractCommand
{

    public function run(): void
    {
        $this->application->displayConstants();
    }
}
