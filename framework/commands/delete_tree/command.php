<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "delete-tree")]
#[CommandDeclaration(desc: "Delete the application tree.")]
class CreateTree extends AbstractAttributedCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->deleteTree();
    }
}
