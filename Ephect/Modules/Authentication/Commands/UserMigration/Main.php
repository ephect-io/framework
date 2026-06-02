<?php

namespace Ephect\Modules\Authentication\Commands\UserMigration;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "migrate", subject: "user")]
#[CommandDeclaration(longArgs: ["up", "down"])]
#[CommandDeclaration(shortArgs: ["u", "d"])]
#[CommandDeclaration(desc: "Create or delete the User table.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $arg = $this->application->getArgi(2);
        
        $use = new Lib($this->application);
        $use->execute();

        return 0;
    }
}
