<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "os")]
#[CommandDeclaration(desc: "Display the running operating system name.")]
class Os extends AbstractAttributedCommand
{
    public function run(): void
    {
        $data = $this->application->getOS();
        $this->application->writeLine($data);
    }
}
