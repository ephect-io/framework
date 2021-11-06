<?php

namespace Ephect\Apps\Egg;

use Ephect\Apps\Egg\EggLib;
use Ephect\CLI\Application;

include 'includes.php';
include 'lib.php';

class Program extends Application
{

    public static function main($argv, $argc)
    {
        (new Program($argc, $argv));
    }

    public function __construct($argc, $argv)
    {
        parent::__construct($argv, $argc);
    }

}

Program::main($argv, $argc);
