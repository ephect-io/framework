<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "title")]
#[CommandDeclaration(desc: "Display the running application title.")]
class Title extends AbstractAttributedCommand
{
    public function run(): void
    {
        $data = $this->application->getTitle();
        $this->application->writeLine($data);
    }
}
