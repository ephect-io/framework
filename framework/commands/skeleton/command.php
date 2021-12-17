<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "skeleton")]
#[CommandDeclaration(desc: "Create the skeleton application tree.")]
class CreateSkeleton extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->createCommonTrees();
        $egg->createSkeleton();
    }
}
