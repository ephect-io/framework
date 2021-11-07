<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "title")]
#[CommandDeclaration(desc: "Display the running application title.")]
class Title extends AbstractCommand
{
    public function run(): void
    {
        $data = $this->application->getTitle();
        $this->application->writeLine($data);
    }
}
