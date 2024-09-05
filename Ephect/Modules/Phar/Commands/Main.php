<?php

namespace Ephect\Phar\Commands;

use Ephect\Phar\PharLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "master-phar")]
#[CommandDeclaration(desc: "Make a phar archive of the current application with files from the master repository.")]
#[CommandDeclaration(isPhar: IS_PHAR_APP)]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $phar = new PharLib($this->application);
        $phar->makeMasterPhar();

        return 0;
    }
}
