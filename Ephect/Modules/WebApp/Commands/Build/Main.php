<?php

namespace Ephect\Modules\WebApp\Commands\Build;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "build")]
#[CommandDeclaration(desc: "Build the application.")]
class Main extends AbstractCommand
{
    public function run(): int
    {
        $principal = include_once __DIR__ . DIRECTORY_SEPARATOR  . "Logo.php";
        echo $principal . PHP_EOL;

        $egg = new Lib($this->application);
        $egg->build();

        return 0;
    }
}
