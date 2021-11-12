<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\PharLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "phar")]
#[CommandDeclaration(desc: "Make a phar archive of the current application with files in vendor directory.")]
#[CommandDeclaration(isPhar: IS_PHAR_APP)]
class MakePhar extends AbstractCommand
{
    public function run(): void
    {
        $phar = new PharLib($this->application);
        $phar->makeVendorPhar();
    }
}
