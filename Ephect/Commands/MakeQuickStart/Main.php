<?php

namespace Ephect\Commands\CreateQuickstart;

use Ephect\Commands\CommonLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "quickstart")]
#[CommandDeclaration(desc: "Create the quickstart application tree.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new CommonLib($this->application);
        $egg->createCommonTrees();

        $lib = new Lib($this->application);
        $lib->createQuickstart();
    }
}
