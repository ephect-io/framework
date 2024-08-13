<?php

namespace Ephect\Commands\ModuleUpdate;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "module", subject: "update")]
#[CommandDeclaration(desc: "Composer update wrapper and more.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $lib = new Lib($this->application);
        $lib->update();

        return 0;
    }
}
