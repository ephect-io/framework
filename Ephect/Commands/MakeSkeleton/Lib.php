<?php

namespace Ephect\Commands\MakeSkeleton;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\Utils\File;

class Lib extends AbstractCommandLib
{

    public function makeSkeleton(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Skeleton';

        File::safeMkDir(SRC_ROOT);
        $destDir = realpath(SRC_ROOT);

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }

        $tree = File::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            File::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}

