<?php

namespace Ephect\Commands\InfoModules;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "show", subject: "modules")]
#[CommandDeclaration(desc: "Display the module section of phpinfo() output.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $info = new PhpInfo();
        $data = $info->getModulesSection(true);
        Console::writeLine($data);
    }
}
