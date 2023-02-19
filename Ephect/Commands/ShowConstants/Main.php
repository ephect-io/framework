<?php

namespace Ephect\Commands\Constants;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "constants")]
#[CommandDeclaration(desc: "Display the application constants.")]
class Main extends AbstractCommand
{

    public function run(): void
    {
        $lib = new Lib($this->application);
        $lib->displayConstants();
    }
}
