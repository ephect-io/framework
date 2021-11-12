<?php

namespace Ephect\Commands;

use Ephect\CLI\Console;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "modules")]
#[CommandDeclaration(desc: "Display the module section of phpinfo() output.")]
class InfoModules extends AbstractCommand
{
    public function run(): void
    {
        $info = new PhpInfo();
        $data = $info->getModulesSection(true);
        Console::writeLine($data);
    }
}
