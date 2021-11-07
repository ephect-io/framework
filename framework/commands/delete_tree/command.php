<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "delete", subject: "tree")]
#[CommandDeclaration(desc: "Delete the application tree.")]
class DeleteTree extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->deleteTree();
    }
}
