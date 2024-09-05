<?php

namespace Ephect\Modules\Samples\Commands\MakeQuickStart;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;
use Ephect\Modules\Samples\Commands\MakeQuickStart\Lib;
use Ephect\Samples\Common;

#[CommandDeclaration(verb: "make", subject: "quickstart")]
#[CommandDeclaration(desc: "Create the quickstart application tree.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $egg = new Common;
        $egg->createCommonTrees();

        $lib = new Lib($this->application);
        $lib->createQuickstart();

        return 0;
    }
}
