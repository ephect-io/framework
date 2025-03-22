<?php

namespace Ephect\Modules\Samples\Commands\MakeSkeleton;

use Ephect\Framework\CLI\Console;
use Ephect\Framework\CLI\ConsoleColors;
use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Utils\File;
use Ephect\Samples\Common;

class Lib extends AbstractCommandLib
{

    public function makeSkeleton(): void
    {
        Console::writeLine(ConsoleColors::getColoredString("Publishing QuickStart files...", ConsoleColors::BLUE));

        $sample = Common::getModuleSrcDir() . 'Assets' . DIRECTORY_SEPARATOR . 'Skeleton';

        File::safeMkDir(siteSrcPath());
        $destDir = realpath(siteSrcPath());

        if (!file_exists($sample) || !file_exists($destDir)) {
            Console::writeLine("Stopping! Sample dir %s or destination dir %s does not exist.", $sample, $destDir);
            return;
        }

        $tree = File::walkTreeFiltered($sample);

        Console::writeLine(ConsoleColors::getColoredString("Source directory: $sample", ConsoleColors::GREEN));
        Console::writeLine(ConsoleColors::getColoredString("Destination directory: $destDir", ConsoleColors::GREEN));

        foreach ($tree as $filePath) {
            Console::writeLine("Copying file: %s", $filePath);
            File::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}

