<?php

namespace Ephect\Modules\Samples\Commands\MakeQuickStart;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\Enums\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Modules\Utils;
use Ephect\Modules\Samples\Commands\Common;

class Lib extends AbstractCommandLib
{
    public function createQuickstart(): void
    {
        Console::writeLine("Publishing Skeleton files...", ConsoleColors::BLUE);

        $utils = new Utils(dirname(__DIR__));

        $sample = $utils->getModuleSrcDir() . 'Assets' . DIRECTORY_SEPARATOR . 'QuickStart';

        File::safeMkDir(siteSrcPath());
        $destDir = realpath(siteSrcPath());

        $common = new Common();
        $common->publishFiles($sample, $destDir);
    }
}
