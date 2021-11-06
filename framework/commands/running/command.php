<?php

namespace Ephect\Commands;

class Running extends AbstractCommand
{

    public function getCommand(): CommandStructure
    {
        return new CommandStructure([
            'long' => 'running',
            'short' => '',
            'description' => 'Show Phar::running() output',
            'callback' => function () {
                $this->_application->writeLine(\Phar::running());
            }
        ]);
    }
}
