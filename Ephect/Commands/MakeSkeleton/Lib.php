<?php

namespace Ephect\Commands\MakeSkeleton;

use Ephect\Framework\Commands\AbstractCommandLib;
use Ephect\Framework\IO\Utils;

class Lib extends AbstractCommandLib
{

    public function makeSkeleton(): void
    {
        $sample = EPHECT_ROOT . 'Samples' . DIRECTORY_SEPARATOR . 'Skeleton';

        Utils::safeMkDir(SRC_ROOT);
        $destDir = realpath(SRC_ROOT);

        if (!file_exists($sample) || !file_exists($destDir)) {
            return;
        }

        $tree = Utils::walkTreeFiltered($sample);

        foreach ($tree as $filePath) {
            Utils::safeWrite($destDir . $filePath, '');
            copy($sample . $filePath, $destDir . $filePath);
        }
    }
}

