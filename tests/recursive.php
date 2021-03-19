<?php

namespace Ephect\Core;

include  dirname(__DIR__) . '/framework/bootstrap.php';

use Ephect\CLI\Application;
use Ephect\Components\Generators\ComponentParser;
use Ephect\Components\PreHtml;
use Ephect\IO\Utils;

class Program extends Application
{
    public static function main($argv, $argc)
    {
        (new Program)->run($argv);
    }

    public function run(?array ...$params): void
    {
        $str = <<<HTML
HTML;

        $prehtml = new PreHtml($str);
        $cp = new ComponentParser($prehtml);
        $matches = $cp->doComponents();

        $json = json_encode($matches, JSON_PRETTY_PRINT);

        Utils::safeWrite('doc2.json', $json);
    }
}

Program::main($argv, $argc);
