<?php

namespace Ephect\Commands\Serve;

use Ephect\Apps\Egg\EggLib;
use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\CLI\System\Command;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\IO\Utils;

class Lib extends AbstractCommandLib
{

    public function Serve(): void
    {
  
        $egg = new EggLib($this->parent);
        $port = $egg->getPort();

        Utils::safeWrite(CONFIG_DIR . 'dev_port', $port);

        $cmd = new Command();
        $php = $cmd->which('php');

        Console::writeLine('PHP is %s', ConsoleColors::getColoredString($php, ConsoleColors::RED));
        Console::writeLine('Port is %s', ConsoleColors::getColoredString($port, ConsoleColors::RED));
        $cmd->execute($php, '-S', "localhost:$port", '-t', 'public');
        Console::writeLine("Serving the application locally ...");
    }


}

