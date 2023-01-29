<?php

namespace Ephect\Commands\CreateSkeleton;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "skeleton")]
#[CommandDeclaration(desc: "Create the skeleton application tree.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->createCommonTrees();
        $egg->createSkeleton();
    }
}
