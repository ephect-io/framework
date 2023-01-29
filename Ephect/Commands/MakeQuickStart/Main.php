<?php

namespace Ephect\Commands\CreateQuickstart;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "quickstart")]
#[CommandDeclaration(desc: "Create the quickstart application tree.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->createCommonTrees();
        $egg->createQuickstart();
    }
}
