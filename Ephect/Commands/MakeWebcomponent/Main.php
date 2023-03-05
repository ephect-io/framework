<?php

namespace Ephect\Commands\MakeWebComponent;

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(verb: "make", subject: "webcomponent")]
#[CommandDeclaration(desc: "Create the base tree of a webComponent.")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        $lib = new Lib($this->application);
        $lib->createWebComponentBase();
    }
}
