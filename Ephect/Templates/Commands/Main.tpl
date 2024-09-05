<?php

namespace Ephect\Commands\{{CommandNamespace}};

use Ephect\Framework\Commands\AbstractCommand;
use Ephect\Framework\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration({{CommandAttributes}})]
#[CommandDeclaration(desc: "{{Description}}")]
class Main extends AbstractCommand
{
    public function run(): void
    {
        {{GetArgs}}
        $lib = new Lib($this->application);
        $lib->{{MethodName}}({{SetArgs}});
    }
}
