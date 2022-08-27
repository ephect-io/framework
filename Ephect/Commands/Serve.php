<?php

namespace Ephect\Commands;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\System\Command;
use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "serve")]
#[CommandDeclaration(desc: "Launch PHP embedded server on available port starting from the one in config.")]
class Serve extends AbstractCommand
{

    public function run(): void
    {

        $egg = new EggLib($this->application);
        $egg->serve();
    }
}
