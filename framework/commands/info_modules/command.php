<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "info-modules")]
#[CommandDeclaration(desc: "Display the module section of phpinfo() output.")]
class InfoModules extends AbstractAttributedCommand
{
    public function run(): void
    {
        $info = new PhpInfo();
        $data = $info->getModulesSection(true);
        $this->application->writeLine($data);
    }
}
