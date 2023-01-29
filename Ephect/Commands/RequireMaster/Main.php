<?php

namespace Ephect\Commands\RequireMaster;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "wget", subject: "master-branch")]
#[CommandDeclaration(desc: "Download the ZIP file of the master branch of Ephect framework.")]
#[CommandDeclaration(isPhar: IS_PHAR_APP)]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $result = $egg->requireMaster();

        Console::writeLine($result->tree);
    }
}
