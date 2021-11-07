<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "create", subject: "tree")]
#[CommandDeclaration(desc: "Create the application tree.")]
class CreateTree extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->createTree();
    }
}
