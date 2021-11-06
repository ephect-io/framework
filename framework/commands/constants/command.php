<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "constants")]
#[CommandDeclaration(desc: "Display the application constants.")]
class Constants extends AbstractAttributedCommand
{

    public function run(): void
    {
        $this->application->displayConstants();
    }
}
