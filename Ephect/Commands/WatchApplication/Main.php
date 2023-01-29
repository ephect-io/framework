<?php

namespace Ephect\Commands\WatchApplication;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "watch")]
#[CommandDeclaration(desc: "Watch the application.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $egg = new EggLib($this->application);
        $egg->watch();
    }
}
