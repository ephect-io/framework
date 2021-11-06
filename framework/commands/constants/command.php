<?php

namespace Ephect\Commands;

class Constants extends AbstractCommand
{

    public function getCommand(): CommandStructure
    {
        return new CommandStructure([
            'long' => 'constants',
            'short' => '',
            'description' => 'Display the application constants.',
            'callback' => function () {
                $data = $this->_application->displayConstants();
            }
        ]);
    }
}
