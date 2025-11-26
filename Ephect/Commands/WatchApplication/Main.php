<?php

namespace Ephect\Commands\WatchApplication;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "watch")]
#[CommandDeclaration(desc: "Watch the application.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $use = new Lib($this->application);
        $use->watch();

        return 0;
    }
}
