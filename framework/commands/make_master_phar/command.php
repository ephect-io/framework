<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "make-master-phar")]
#[CommandDeclaration(desc: "Make a phar archive of the current application with files from the master repository.")]
#[CommandDeclaration(isPhar: IS_PHAR_APP)]
class MakeMasterPhar extends AbstractCommand
{
    public function run(): void
    {
        $this->application->makeMasterPhar();
    }
}
