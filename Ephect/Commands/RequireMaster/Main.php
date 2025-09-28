<?php

namespace Ephect\Commands\RequireMaster;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "wget", subject: "master-branch")]
#[CommandDeclaration(desc: "Download the ZIP file of the master branch of Ephect framework.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $egg = new Lib($this->application);
        $result = $egg->requireMaster();

        Console::writeLine($result->tree);

        return 0;
    }
}
