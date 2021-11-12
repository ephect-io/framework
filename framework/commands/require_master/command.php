<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "wget", subject: "master-branch")]
#[CommandDeclaration(desc: "Download the ZIP file of the master branch of Ephect framework.")]
#[CommandDeclaration(isPhar: IS_PHAR_APP)]
class RequireMaster extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->requireMaster();
    }
}
