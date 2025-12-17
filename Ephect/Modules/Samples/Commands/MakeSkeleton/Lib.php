<?php

namespace Ephect\Modules\Samples\Commands\MakeSkeleton;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Utils\File;
use Ephect\Framework\Modules\Utils;
use Ephect\Modules\Samples\Commands\Common;

class Lib extends AbstractCommandLib
{
    public function makeSkeleton(): void
    {

        $utils = new Utils(dirname(__DIR__));

        Console::writeLine(ConsoleColors::getColoredString("Publishing QuickStart files...", ConsoleColors::BLUE));

        $sample = $utils->getModuleSrcDir() . 'Assets' . DIRECTORY_SEPARATOR . 'Skeleton';

        File::safeMkDir(siteSrcPath());
        $destDir = realpath(siteSrcPath());

        $common = new Common();
        $common->publishFiles($sample, $destDir);
    }
}
