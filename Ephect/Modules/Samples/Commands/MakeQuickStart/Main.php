<?php

namespace Ephect\Modules\Samples\Commands\MakeQuickStart;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Modules\Samples\Commands\Common;
use Ephect\Modules\Samples\Commands\MakeQuickStart\Lib;

#[CommandDeclaration(verb: "make", subject: "quickstart")]
#[CommandDeclaration(desc: "Create the quickstart application tree.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $use = new Common();
        $use->createCommonTrees();

        $lib = new Lib($this->application);
        $lib->createQuickstart();

        return 0;
    }
}
