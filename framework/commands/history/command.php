<?php

namespace Ephect\Commands;

use Ephect\Commands\Attributes\CommandDeclaration;

#[CommandDeclaration(long: "display-history")]
#[CommandDeclaration(desc: "Display the commands history.")]
class History extends AbstractAttributedCommand
{
    public function run(): void
    {
        $history = readline_list_history();
        $this->application->displayTree($history);
    }
}
