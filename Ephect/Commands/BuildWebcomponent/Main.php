<?php

namespace Ephect\Commands\BuildWebcomponent;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "build", subject: "webcomponent")]
#[CommandDeclaration(desc: "Build client-side components.")]
class Main extends AbstractCommand
{
    public function run(): void
    {

        $egg = new Lib($this->application);
        $egg->build();
    }
}
