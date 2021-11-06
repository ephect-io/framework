<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "create-tree")]
#[CommandDeclaration(desc: "Create the application tree.")]
class CreateTree extends AbstractAttributedCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->createTree();
    }
}
